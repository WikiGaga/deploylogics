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
            ->get();
        if ($orderDetails->isEmpty()) {
            return response()->json([
                'success' => true,
                'message' => 'No order details found for the supplied filters.',
                'inserted_rows' => 0,
                'data' => [],
            ]);
        }

        $optionListIds = [];
        $structuredDetails = [];

        foreach ($orderDetails as $detail) {
            $variations = $this->decodeVariation($detail->variation);

            if (empty($variations)) {
                continue;
            }

            $optionIds = [];
            foreach ($variations as $variation) {
                $optionIds = array_merge($optionIds, $this->extractOptionListIds($variation));
            }

            if (empty($optionIds)) {
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
            return response()->json([
                'success' => true,
                'message' => 'No option list identifiers resolved from order variations.',
                'inserted_rows' => 0,
                'data' => [],
            ]);
        }

        $recipes = FoodRecipe::with('dtls')
            ->where('branch_id', $branchId)
            ->whereIn('food_id', $optionListIds)
            ->orderBy('recipe_date', 'desc')
            ->get()
            ->groupBy('food_id')
            ->map(function ($group) {
                return $group->first();
            });

        if ($recipes->isEmpty()) {
            return response()->json([
                'success' => true,
                'message' => 'No recipes found for the supplied option list identifiers.',
                'inserted_rows' => 0,
                'data' => [],
            ]);
        }

        $rowsToPersist = [];

        foreach ($structuredDetails as $detail) {
            foreach ($detail['option_list_ids'] as $optionListId) {
                $recipe = $recipes->get($optionListId);

                if (!$recipe || $recipe->dtls->isEmpty()) {
                    continue;
                }

                foreach ($recipe->dtls as $component) {
                    $rowsToPersist[] = [
                        'order_detail_id' => $detail['order_detail_id'],
                        'order_id'        => $detail['order_id'],
                        'restaurant_id'   => $branchId,
                        'option_list_id'  => $optionListId,
                        'food_recipe_id'  => $recipe->id,
                        'food_id'         => $detail['food_id'],
                        'product_id'      => $component->product_id,
                        'product_quantity'=> round((float) $component->quantity * $detail['order_quantity'], 4),
                        'usage_date'      => $detail['order_date']->toDateString(),
                        'measure_unit'    => $component->uom_id ?? null,
                    ];
                }
                dd($rowsToPersist);
            }
        }

        if (empty($rowsToPersist)) {
            return response()->json([
                'success' => true,
                'message' => 'No ingredient usage rows were generated for persistence.',
                'inserted_rows' => 0,
                'data' => [],
            ]);
        }

        $summary = [];

        DB::beginTransaction();

        try {
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
                        'product_quantity'=> $row['product_quantity'],
                        'usage_date'      => $row['usage_date'],
                        'measure_unit'    => $row['measure_unit'],
                    ]
                );

                $summaryKey = $row['product_id'].'-'.$row['usage_date'];

                if (!isset($summary[$summaryKey])) {
                    $summary[$summaryKey] = [
                        'product_id'      => $row['product_id'],
                        'food_id'         => $row['food_id'],
                        'usage_date'      => $row['usage_date'],
                        'total_quantity'  => 0,
                        'order_count'     => 0,
                    ];
                }

                $summary[$summaryKey]['total_quantity'] += $row['product_quantity'];
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
            'data'          => array_values($summary),
        ]);
    }
    private function decodeVariation($payload): array
    {
        if (empty($payload)) {
            return [];
        }

        if (is_array($payload)) {
            return $payload;
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

        return $decoded;
    }

    private function extractOptionListIds(array $variation): array
    {
        $ids = [];

        if (!empty($variation['option_list_id'])) {
            $ids[] = (int) $variation['option_list_id'];
        }

        if (!empty($variation['options_list_id'])) {
            $ids[] = (int) $variation['options_list_id'];
        }

        if (!empty($variation['values']) && is_array($variation['values'])) {
            foreach ($variation['values'] as $value) {
                if (!is_array($value)) {
                    continue;
                }

                if (!empty($value['options_list_id'])) {
                    $ids[] = (int) $value['options_list_id'];
                } elseif (!empty($value['option_list_id'])) {
                    $ids[] = (int) $value['option_list_id'];
                }
            }
        }

        return array_values(array_unique(array_filter($ids)));
    }
}



