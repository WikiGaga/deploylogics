<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FoodRecipe;
use App\Models\OrderRecipeUsage;
use App\Models\TblPurcProduct;
use App\Models\TblPurcProductBarcode;
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
            'no_addon_recipe' => 0,
            'recipe_not_effective' => 0,
            'empty_recipe' => 0,
            'invalid_product' => 0,
            'option_list_ids' => [],
            'recipe_not_effective_ids' => [],
        ];

        $orderDetails = DB::table('order_details as od')
            ->join('orders as o', 'o.id', '=', 'od.order_id')
            ->select([
                'od.id as order_detail_id',
                'od.order_id',
                'od.quantity',
                'od.variation',
                'od.add_ons',
                'od.food_id',
                'o.restaurant_id',
                'o.order_date',
            ])
            ->where('o.restaurant_id', $branchId)
            ->whereBetween('o.order_date', [$dateFrom, $dateTo])
            ->whereRaw("LOWER(TRIM(o.payment_status)) = 'paid'")
            ->where(function ($query) {
                $query->whereNull('o.order_status')
                    ->orWhereRaw("LOWER(TRIM(o.order_status)) NOT IN ('canceled', 'cancelled')");
            })
            ->where(function ($query) {
                $query->whereNull('od.is_deleted')
                    ->orWhereRaw("UPPER(od.is_deleted) <> 'Y'");
            })
            ->get();

        if ($orderDetails->isEmpty()) {
            $deletedStale = $this->deleteUsageForRange($branchId, $dateFrom, $dateTo);

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
        $addOnIds = [];
        $structuredDetails = [];

        foreach ($orderDetails as $detail) {
            $variations = $this->decodeVariation($detail->variation);
            $addons = $this->extractAddOns($variations, $detail->add_ons);
            if (empty($variations) && empty($addons)) {
                $unmatched['no_variation']++;
                continue;
            }

            $optionIds = $this->extractOptionListIdsFromVariations($variations);

            if (empty($optionIds) && empty($addons)) {
                $unmatched['no_option_ids']++;
                continue;
            }

            $structuredDetails[] = [
                'order_detail_id' => (int) $detail->order_detail_id,
                'order_id'        => (int) $detail->order_id,
                'order_quantity'  => (float) $detail->quantity,
                'food_id'         => (int) ($detail->food_id ?? 0),
                'option_list_ids' => $optionIds,
                'addons' => $addons,
                'order_date'      => Carbon::parse($detail->order_date),
            ];

            $optionListIds = array_merge($optionListIds, $optionIds);
            $addOnIds = array_merge($addOnIds, array_keys($addons));
        }

        $optionListIds = array_values(array_unique(array_filter($optionListIds)));
        $addOnIds = array_values(array_unique(array_filter($addOnIds)));

        if (empty($optionListIds) && empty($addOnIds)) {
            return response()->json([
                'success' => false,
                'message' => 'No option list identifiers resolved from order variations.',
                'inserted_rows' => 0,
                'stale_rows_removed' => 0,
                'existing_usage_preserved' => true,
                'unmatched' => $unmatched,
                'data' => [],
            ], 422);
        }

        $recipeQuery = function ($ids, $type) use ($branchId) {
            if (empty($ids)) return collect();
            return FoodRecipe::with('dtls')
                ->where('branch_id', $branchId)
                ->whereIn('food_id', $ids)
                ->where('recipe_type', $type)
                ->orderBy('recipe_date', 'desc')
                ->orderBy('id', 'desc')
                ->get()->groupBy('food_id');
        };
        $recipesByFood = $recipeQuery($optionListIds, 'option');
        $recipesByAddOn = $recipeQuery($addOnIds, 'addon');

        if ($recipesByFood->isEmpty() && $recipesByAddOn->isEmpty()) {
            $unmatched['no_recipe'] = count($optionListIds);
            $unmatched['no_addon_recipe'] = count($addOnIds);
            $unmatched['option_list_ids'] = array_slice($optionListIds, 0, 50);

            return response()->json([
                'success' => false,
                'message' => 'No recipes found for the supplied option list identifiers.',
                'inserted_rows' => 0,
                'stale_rows_removed' => 0,
                'existing_usage_preserved' => true,
                'unmatched' => $unmatched,
                'data' => [],
            ], 422);
        }

        $rowsToPersist = [];
        $resolvedProductIds = [];

        foreach ($structuredDetails as $detail) {
            $targets = [];
            foreach ($detail['option_list_ids'] as $optionListId) {
                $targets[] = ['id' => $optionListId, 'type' => 'option', 'quantity' => $detail['order_quantity']];
            }
            foreach ($detail['addons'] as $addOnId => $quantity) {
                $targets[] = ['id' => $addOnId, 'type' => 'addon', 'quantity' => $quantity];
            }
            foreach ($targets as $target) {
                $optionListId = $target['id'];
                $recipeGroups = $target['type'] === 'addon' ? $recipesByAddOn : $recipesByFood;
                $recipeGroup = $recipeGroups->get($optionListId);
                $recipe = $this->pickRecipeAsOf($recipeGroups, $optionListId, $detail['order_date']);

                if (!$recipe) {
                    if ($recipeGroup && $recipeGroup->isNotEmpty()) {
                        $unmatched['recipe_not_effective']++;
                        $unmatched['recipe_not_effective_ids'][] = $optionListId;
                    } else {
                        if ($target['type'] === 'addon') {
                            $unmatched['no_addon_recipe']++;
                        } else {
                            $unmatched['no_recipe']++;
                            $unmatched['option_list_ids'][] = $optionListId;
                        }
                    }
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
                    $productQtyUsed = round($productQty * $packing * $target['quantity'], 4);
                    $storedProductId = trim((string) $component->product_id);
                    if (!array_key_exists($storedProductId, $resolvedProductIds)) {
                        $resolvedProductIds[$storedProductId] = $this->resolveInventoryProductId($storedProductId);
                    }
                    $productId = $resolvedProductIds[$storedProductId];
                    if (!$productId) {
                        $unmatched['invalid_product']++;
                        continue;
                    }
                    $rowKey = $detail['order_detail_id'] . '-' . $productId;

                    if (!isset($rowsToPersist[$rowKey])) {
                        $rowsToPersist[$rowKey] = [
                            'order_detail_id' => $detail['order_detail_id'],
                            'order_id'        => $detail['order_id'],
                            'restaurant_id'   => $branchId,
                            'option_list_id'  => $target['type'] === 'option' ? $optionListId : null,
                            'food_recipe_id'  => $recipe->id,
                            'food_id'         => $detail['food_id'],
                            'product_id'      => $productId,
                            'order_quantity'  => $orderQty,
                            'product_quantity'=> round($productQty * $packing, 4),
                            'product_quantity_used'=> $productQtyUsed,
                            'usage_date'      => $detail['order_date']->toDateString(),
                            'measure_unit'    => $component->uom_id ?? null,
                            '_sources'       => [],
                        ];
                    } else {
                        $rowsToPersist[$rowKey]['product_quantity_used'] = round(
                            $rowsToPersist[$rowKey]['product_quantity_used'] + $productQtyUsed,
                            4
                        );
                    }
                    $sourceKey = $target['type'] . ':' . $optionListId . ':' . $recipe->id;
                    if (!isset($rowsToPersist[$rowKey]['_sources'][$sourceKey])) {
                        $rowsToPersist[$rowKey]['_sources'][$sourceKey] = [
                            'type' => $target['type'],
                            'target_id' => (int) $optionListId,
                            'food_recipe_id' => (int) $recipe->id,
                            'target_quantity' => (float) $target['quantity'],
                            'recipe_quantity' => 0,
                            'used_quantity' => 0,
                        ];
                    }
                    $source = &$rowsToPersist[$rowKey]['_sources'][$sourceKey];
                    $source['recipe_quantity'] = round($source['recipe_quantity'] + ($productQty * $packing), 4);
                    $source['used_quantity'] = round($source['used_quantity'] + $productQtyUsed, 4);
                    unset($source);
                }
            }
        }

        foreach ($rowsToPersist as &$row) {
            $row = $this->finalizeUsageRow($row);
        }
        unset($row);

        $unmatched['option_list_ids'] = array_values(array_unique($unmatched['option_list_ids']));
        $unmatched['option_list_ids'] = array_slice($unmatched['option_list_ids'], 0, 50);
        $unmatched['recipe_not_effective_ids'] = array_values(array_unique($unmatched['recipe_not_effective_ids']));
        $unmatched['recipe_not_effective_ids'] = array_slice($unmatched['recipe_not_effective_ids'], 0, 50);

        if (empty($rowsToPersist)) {
            return response()->json([
                'success' => false,
                'message' => 'No ingredient usage rows were generated for persistence.',
                'inserted_rows' => 0,
                'stale_rows_removed' => 0,
                'existing_usage_preserved' => true,
                'unmatched' => $unmatched,
                'data' => [],
            ], 422);
        }

        $summary = [];
        $deletedStale = 0;

        DB::beginTransaction();

        try {
            // Rebuild the selected range so changes to order quantities,
            // variations, or recipes cannot leave stale ingredient rows behind.
            $deletedStale = $this->deleteUsageForRange($branchId, $dateFrom, $dateTo);

            foreach ($rowsToPersist as $row) {
                OrderRecipeUsage::create($row);

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

    private function deleteUsageForRange($branchId, Carbon $dateFrom, Carbon $dateTo)
    {
        return OrderRecipeUsage::where('restaurant_id', $branchId)
            ->whereBetween('usage_date', [$dateFrom->toDateString(), $dateTo->toDateString()])
            ->delete();
    }

    private function finalizeUsageRow(array $row): array
    {
        $sources = array_values($row['_sources']);
        unset($row['_sources']);
        $row['source_breakdown'] = json_encode($sources);
        if (count($sources) !== 1) {
            $row['option_list_id'] = null;
            $row['food_recipe_id'] = null;
        }
        // Effective base quantity per ordered food item. The authoritative
        // stock movement remains PRODUCT_QUANTITY_USED at four decimals.
        $row['product_quantity'] = $row['order_quantity'] > 0
            ? round($row['product_quantity_used'] / $row['order_quantity'], 4)
            : $row['product_quantity_used'];
        return $row;
    }

    /**
     * Recipe detail rows historically stored the selected barcode in product_id.
     * Keep old recipes usable, but always persist the inventory product's real ID.
     */
    private function resolveInventoryProductId($storedValue): ?int
    {
        $storedValue = trim((string) $storedValue);
        if ($storedValue === '') {
            return null;
        }

        $productId = TblPurcProduct::where('product_id', $storedValue)->value('product_id');
        if ($productId !== null) {
            return (int) $productId;
        }

        $barcodeProductId = TblPurcProductBarcode::where('product_barcode_barcode', $storedValue)
            ->value('product_id');

        if ($barcodeProductId === null && ctype_digit($storedValue)) {
            $barcodeProductId = TblPurcProductBarcode::where('product_barcode_id', $storedValue)
                ->value('product_id');
        }

        return $barcodeProductId === null ? null : (int) $barcodeProductId;
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

        // Some POS clients store the variation payload double-encoded.
        if (json_last_error() === JSON_ERROR_NONE && is_string($decoded)) {
            $decoded = json_decode($decoded, true);
        }

        if (json_last_error() !== JSON_ERROR_NONE || !is_array($decoded)) {
            return [];
        }

        // Other clients wrap the actual variation list in a `data` key.
        if (isset($decoded['data']) && is_array($decoded['data'])) {
            $decoded = $decoded['data'];
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
            $this->collectOptionListIds($variation, $ids);
        }

        return array_values(array_unique(array_filter($ids)));
    }

    /** Add-on quantities in POS JSON are totals for the order line. */
    private function extractAddOns(array $variations, $topLevelPayload): array
    {
        $nested = [];
        foreach ($variations as $variation) {
            if (is_array($variation) && isset($variation['addons'])) {
                $this->collectAddOns($variation['addons'], $nested);
            }
        }

        // Some clients also write order-level add_ons. Prefer variation values
        // for the same ID, because they may duplicate the order-level snapshot.
        $topLevel = [];
        $this->collectAddOns($topLevelPayload, $topLevel);
        foreach ($topLevel as $id => $quantity) {
            if (!isset($nested[$id])) $nested[$id] = $quantity;
        }
        return $nested;
    }

    private function collectAddOns($payload, array &$quantities): void
    {
        if (is_string($payload)) {
            $payload = json_decode($payload, true);
        }
        if (!is_array($payload)) return;

        foreach ($payload as $addon) {
            if (!is_array($addon) || !isset($addon['id']) || !is_numeric($addon['id'])
                || !isset($addon['quantity']) || !is_numeric($addon['quantity'])) continue;
            $id = (int) $addon['id'];
            $quantity = (float) $addon['quantity'];
            if ($id <= 0 || $quantity <= 0) continue;
            $quantities[$id] = ($quantities[$id] ?? 0) + $quantity;
        }
    }

    /**
     * POS variation structures differ between clients and versions. Walk the
     * complete payload and collect recipe option identifiers wherever nested.
     */
    private function collectOptionListIds($value, array &$ids): void
    {
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            if (json_last_error() !== JSON_ERROR_NONE || !is_array($decoded)) {
                return;
            }
            $value = $decoded;
        }

        if (!is_array($value)) {
            return;
        }

        foreach ($value as $key => $nestedValue) {
            if (($key === 'option_list_id' || $key === 'options_list_id')
                && is_numeric($nestedValue) && (int) $nestedValue > 0) {
                $ids[] = (int) $nestedValue;
                continue;
            }

            if (is_array($nestedValue) || is_string($nestedValue)) {
                $this->collectOptionListIds($nestedValue, $ids);
            }
        }
    }

}
