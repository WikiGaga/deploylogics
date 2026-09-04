<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FoodRecipe;
use App\Models\OrderRecipeUsage;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Throwable;

class OrderRecipeUsageController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'branch_id' => ['required', 'integer'],
            'date_from' => ['required', 'date'],
            'date_to'   => ['required', 'date', 'after_or_equal:date_from'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors(),
            ], 422);
        }

        $branchId = (int) $request->input('branch_id');
        $dateFrom = Carbon::parse($request->input('date_from'))->startOfDay();
        $dateTo   = Carbon::parse($request->input('date_to'))->endOfDay();

        $unmatched = [
            'no_variation' => 0,
            'no_option_ids' => 0,
            'no_recipe' => 0,
            'empty_recipe' => 0,
            'option_list_ids' => [],
        ];

        $cancelledDetailIds = $this->cancelledOrDeletedDetailIds($branchId, $dateFrom, $dateTo);

        $orderDetails = DB::table('order_details as od')
            ->join('orders as o', 'o.id', '=', 'od.order_id')
            ->select([
                'od.id as order_detail_id',
                'od.order_id',
                'od.quantity',
                'od.variation',
                'od.food_id',
                'o.restaurant_id',
                'o.order_date',
            ])
            ->where('o.restaurant_id', $branchId)
            ->whereBetween('o.order_date', [$dateFrom, $dateTo])
            ->where(function ($query) {
                $query->whereNull('o.order_status')
                    ->orWhereRaw("LOWER(o.order_status) NOT IN ('canceled', 'cancelled')");
            })
            ->where(function ($query) {
                $query->whereNull('od.is_deleted')
                    ->orWhereRaw("UPPER(od.is_deleted) <> 'Y'");
            })
            ->get();

        if ($orderDetails->isEmpty()) {
            $deletedStale = $this->deleteUsageForDetailIds($cancelledDetailIds);

            return response()->json([
                'success' => true,
                'message' => 'No eligible order details found for the supplied filters.',
                'inserted_rows' => 0,
                'stale_rows_removed' => $deletedStale,
                'unmatched' => $unmatched,
                'data' => [],
            ]);
        }

        $optionListIds = [];
        $structuredDetails = [];

        foreach ($orderDetails as $detail) {
            $variations = $this->decodeVariation($detail->variation);

            if (empty($variations)) {
                $unmatched['no_variation']++;
                continue;
            }

            $optionIds = $this->extractOptionListIdsFromVariations($variations);

            if (empty($optionIds)) {
                $unmatched['no_option_ids']++;
                continue;
            }

            $structuredDetails[] = [
                'order_detail_id' => (int) $detail->order_detail_id,
                'order_id'        => (int) $detail->order_id,
                'order_quantity'  => (float) $detail->quantity,
                'food_id'         => (int) ($detail->food_id ?? 0),
                'option_list_ids' => $optionIds,
                'order_date'      => Carbon::parse($detail->order_date),
            ];

            $optionListIds = array_merge($optionListIds, $optionIds);
        }

        $optionListIds = array_values(array_unique(array_filter($optionListIds)));

        if (empty($optionListIds)) {
            $deletedStale = $this->deleteUsageForDetailIds($cancelledDetailIds);

            return response()->json([
                'success' => true,
                'message' => 'No option list identifiers resolved from order variations.',
                'inserted_rows' => 0,
                'stale_rows_removed' => $deletedStale,
                'unmatched' => $unmatched,
                'data' => [],
            ]);
        }

        $recipesByFood = FoodRecipe::with('dtls')
            ->where('branch_id', $branchId)
            ->whereIn('food_id', $optionListIds)
            ->orderBy('recipe_date', 'desc')
            ->orderBy('id', 'desc')
            ->get()
            ->groupBy('food_id');

        if ($recipesByFood->isEmpty()) {
            $unmatched['no_recipe'] = count($optionListIds);
            $unmatched['option_list_ids'] = array_slice($optionListIds, 0, 50);
            $deletedStale = $this->deleteUsageForDetailIds($cancelledDetailIds);

            return response()->json([
                'success' => true,
                'message' => 'No recipes found for the supplied option list identifiers.',
                'inserted_rows' => 0,
                'stale_rows_removed' => $deletedStale,
                'unmatched' => $unmatched,
                'data' => [],
            ]);
        }

        $rowsToPersist = [];

        foreach ($structuredDetails as $detail) {
            foreach ($detail['option_list_ids'] as $optionListId) {
                $recipe = $this->pickRecipeAsOf($recipesByFood, $optionListId, $detail['order_date']);

                if (!$recipe) {
                    $unmatched['no_recipe']++;
                    $unmatched['option_list_ids'][] = $optionListId;
                    continue;
                }

                if ($recipe->dtls->isEmpty()) {
                    $unmatched['empty_recipe']++;
                    $unmatched['option_list_ids'][] = $optionListId;
                    continue;
                }

                foreach ($recipe->dtls as $component) {
                    $orderQty = (float) $detail['order_quantity'];
                    $productQty = (float) $component->quantity;
                    $packing = $this->packingMultiplier($component->packing_id);
                    $productQtyUsed = round($productQty * $packing * $orderQty, 4);
                    $productId = (int) $component->product_id;
                    $rowKey = $detail['order_detail_id'] . '-' . $productId;

                    if (!isset($rowsToPersist[$rowKey])) {
                        $rowsToPersist[$rowKey] = [
                            'order_detail_id' => $detail['order_detail_id'],
                            'order_id'        => $detail['order_id'],
                            'restaurant_id'   => $branchId,
                            'option_list_id'  => $optionListId,
                            'food_recipe_id'  => $recipe->id,
                            'food_id'         => $detail['food_id'],
                            'product_id'      => $productId,
                            'order_quantity'  => $orderQty,
                            'product_quantity'=> round($productQty * $packing, 4),
                            'product_quantity_used'=> $productQtyUsed,
                            'usage_date'      => $detail['order_date']->toDateString(),
                            'measure_unit'    => $component->uom_id ?? null,
                        ];
                    } else {
                        $rowsToPersist[$rowKey]['product_quantity'] = round(
                            $rowsToPersist[$rowKey]['product_quantity'] + ($productQty * $packing),
                            4
                        );
                        $rowsToPersist[$rowKey]['product_quantity_used'] = round(
                            $rowsToPersist[$rowKey]['product_quantity_used'] + $productQtyUsed,
                            4
                        );
                    }
                }
            }
        }

        $unmatched['option_list_ids'] = array_values(array_unique($unmatched['option_list_ids']));
        $unmatched['option_list_ids'] = array_slice($unmatched['option_list_ids'], 0, 50);

        if (empty($rowsToPersist)) {
            $deletedStale = $this->deleteUsageForDetailIds($cancelledDetailIds);

            return response()->json([
                'success' => true,
                'message' => 'No ingredient usage rows were generated for persistence.',
                'inserted_rows' => 0,
                'stale_rows_removed' => $deletedStale,
                'unmatched' => $unmatched,
                'data' => [],
            ]);
        }

        $summary = [];
        $deletedStale = 0;

        DB::beginTransaction();

        try {
            $deletedStale = $this->deleteUsageForDetailIds($cancelledDetailIds);

            foreach ($rowsToPersist as $row) {
                OrderRecipeUsage::updateOrCreate(
                    [
                        'order_detail_id' => $row['order_detail_id'],
                        'product_id'      => $row['product_id'],
                    ],
                    [
                        'order_id'        => $row['order_id'],
                        'restaurant_id'   => $row['restaurant_id'],
                        'food_id'         => $row['food_id'],
                        'option_list_id'  => $row['option_list_id'],
                        'food_recipe_id'  => $row['food_recipe_id'],
                        'order_quantity'  => $row['order_quantity'],
                        'product_quantity'=> $row['product_quantity'],
                        'product_quantity_used'=> $row['product_quantity_used'],
                        'usage_date'      => $row['usage_date'],
                        'measure_unit'    => $row['measure_unit'],
                    ]
                );

                $summaryKey = $row['product_id'].'-'.$row['usage_date'];

                if (!isset($summary[$summaryKey])) {
                    $summary[$summaryKey] = [
                        'product_id'      => $row['product_id'],
                        'usage_date'      => $row['usage_date'],
                        'total_quantity'  => 0,
                        'order_count'     => 0,
                    ];
                }

                $summary[$summaryKey]['total_quantity'] += $row['product_quantity_used'];
                $summary[$summaryKey]['order_count'] += 1;
            }
            DB::commit();
        } catch (Throwable $exception) {
            DB::rollBack();
            Log::error('Failed to persist order recipe usage rows', [
                'message' => $exception->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to persist ingredient usage rows.',
                'error'   => $exception->getMessage(),
            ], 500);
        }

        return response()->json([
            'success'       => true,
            'inserted_rows' => count($rowsToPersist),
            'stale_rows_removed' => $deletedStale,
            'unmatched'     => $unmatched,
            'data'          => array_values($summary),
        ]);
    }

    private function cancelledOrDeletedDetailIds($branchId, Carbon $dateFrom, Carbon $dateTo)
    {
        return DB::table('order_details as od')
            ->join('orders as o', 'o.id', '=', 'od.order_id')
            ->where('o.restaurant_id', $branchId)
            ->whereBetween('o.order_date', [$dateFrom, $dateTo])
            ->where(function ($query) {
                $query->whereRaw("LOWER(o.order_status) IN ('canceled', 'cancelled')")
                    ->orWhereRaw("UPPER(od.is_deleted) = 'Y'");
            })
            ->pluck('od.id')
            ->map(function ($id) {
                return (int) $id;
            })
            ->unique()
            ->values()
            ->all();
    }

    private function deleteUsageForDetailIds(array $detailIds)
    {
        if (empty($detailIds)) {
            return 0;
        }

        $deleted = 0;
        foreach (array_chunk($detailIds, 900) as $chunk) {
            $deleted += OrderRecipeUsage::whereIn('order_detail_id', $chunk)->delete();
        }

        return $deleted;
    }

    private function pickRecipeAsOf($recipesByFood, $optionListId, Carbon $orderDate)
    {
        $group = $recipesByFood->get($optionListId);
        if (!$group) {
            return null;
        }

        foreach ($group as $recipe) {
            if ($this->recipeAppliesOnDate($recipe, $orderDate)) {
                return $recipe;
            }
        }

        return null;
    }

    private function recipeAppliesOnDate($recipe, Carbon $orderDate)
    {
        if (empty($recipe->recipe_date)) {
            return true;
        }

        $recipeDate = $recipe->recipe_date instanceof Carbon
            ? $recipe->recipe_date->copy()->startOfDay()
            : Carbon::parse($recipe->recipe_date)->startOfDay();

        return $recipeDate->lte($orderDate->copy()->startOfDay());
    }

    private function packingMultiplier($packing)
    {
        $packing = (float) $packing;
        if ($packing <= 0) {
            return 1;
        }

        return $packing;
    }

    private function decodeVariation($payload)
    {
        if (empty($payload)) {
            return [];
        }

        if (is_array($payload)) {
            return $this->wrapAssociativeVariation($payload);
        }

        if (!is_string($payload)) {
            return [];
        }

        $payload = trim($payload);

        if ($payload === '' || $payload === 'null') {
            return [];
        }

        $decoded = json_decode($payload, true);

        if (json_last_error() !== JSON_ERROR_NONE || !is_array($decoded)) {
            return [];
        }

        return $this->wrapAssociativeVariation($decoded);
    }

    private function wrapAssociativeVariation(array $decoded)
    {
        if (empty($decoded)) {
            return [];
        }

        if (array_keys($decoded) !== range(0, count($decoded) - 1)) {
            return [$decoded];
        }

        return $decoded;
    }

    private function extractOptionListIdsFromVariations(array $variations)
    {
        $ids = [];

        foreach ($variations as $variation) {
            if (!is_array($variation)) {
                continue;
            }
            $ids = array_merge($ids, $this->extractOptionListIds($variation));
        }

        return array_values(array_unique(array_filter($ids)));
    }

    private function extractOptionListIds(array $variation)
    {
        $ids = [];

        if (!empty($variation['option_list_id'])) {
            $ids[] = (int) $variation['option_list_id'];
        }

        if (!empty($variation['options_list_id'])) {
            $ids[] = (int) $variation['options_list_id'];
        }

        foreach (['values', 'options', 'variation_options'] as $listKey) {
            if (empty($variation[$listKey]) || !is_array($variation[$listKey])) {
                continue;
            }

            foreach ($variation[$listKey] as $value) {
                if (is_string($value)) {
                    $decodedValue = json_decode($value, true);
                    if (json_last_error() === JSON_ERROR_NONE && is_array($decodedValue)) {
                        $value = $decodedValue;
                    }
                }

                if (!is_array($value)) {
                    continue;
                }

                if (!empty($value['options_list_id'])) {
                    $ids[] = (int) $value['options_list_id'];
                }
                if (!empty($value['option_list_id'])) {
                    $ids[] = (int) $value['option_list_id'];
                }
            }
        }

        return array_values(array_unique(array_filter($ids)));
    }
}
