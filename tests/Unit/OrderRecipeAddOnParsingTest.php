<?php

namespace Tests\Unit;

use App\Http\Controllers\Api\OrderRecipeUsageController;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionMethod;

class OrderRecipeAddOnParsingTest extends TestCase
{
    private function addons(array $variations, $topLevel = '[]'): array
    {
        $method = new ReflectionMethod(OrderRecipeUsageController::class, 'extractAddOns');
        $method->setAccessible(true);
        $controller = (new ReflectionClass(OrderRecipeUsageController::class))->newInstanceWithoutConstructor();
        return $method->invoke($controller, $variations, $topLevel);
    }

    public function testNestedAddOnQuantitiesAreTotalsAndRepeatedVariationsAreSummed(): void
    {
        $variations = [
            ['addons' => [['id' => 12, 'quantity' => 2]]],
            ['addons' => [['id' => 12, 'quantity' => 1], ['id' => 5, 'quantity' => 3]]],
        ];
        $this->assertSame([12 => 3.0, 5 => 3.0], $this->addons($variations));
    }

    public function testTopLevelSnapshotDoesNotDoubleCountNestedAddOn(): void
    {
        $variations = [['addons' => [['id' => 12, 'quantity' => 2]]]];
        $topLevel = '[{"id":12,"quantity":2},{"id":7,"quantity":1}]';
        $this->assertSame([12 => 2.0, 7 => 1.0], $this->addons($variations, $topLevel));
    }

    public function testMissingAndNonPositiveQuantitiesAreIgnored(): void
    {
        $this->assertSame([], $this->addons([
            ['addons' => [['id' => 1], ['id' => 2, 'quantity' => 0], ['id' => 3, 'quantity' => -1]]],
        ]));
    }

    public function testOrderQuantityDoesNotAlterSavedAddOnQuantity(): void
    {
        // Live POS example: order quantity 5, add-on quantity 5, total price
        // equals five add-ons rather than twenty-five.
        $addons = $this->addons([['addons' => [['id' => 72, 'quantity' => 5]]]]);
        $this->assertSame(5.0, $addons[72]);
    }

    public function testAggregateUsageKeepsEverySourceAndReconcilesPerItemQuantity(): void
    {
        $method = new ReflectionMethod(OrderRecipeUsageController::class, 'finalizeUsageRow');
        $method->setAccessible(true);
        $controller = (new ReflectionClass(OrderRecipeUsageController::class))->newInstanceWithoutConstructor();
        $row = $method->invoke($controller, [
            'order_quantity' => 2.0,
            'product_quantity_used' => 0.3,
            'product_quantity' => 0.2,
            'option_list_id' => 43,
            'food_recipe_id' => 10,
            '_sources' => [
                'option:43:10' => ['type' => 'option', 'target_id' => 43, 'food_recipe_id' => 10, 'target_quantity' => 2.0, 'recipe_quantity' => 0.1, 'used_quantity' => 0.2],
                'addon:12:11' => ['type' => 'addon', 'target_id' => 12, 'food_recipe_id' => 11, 'target_quantity' => 1.0, 'recipe_quantity' => 0.1, 'used_quantity' => 0.1],
            ],
        ]);
        $this->assertNull($row['option_list_id']);
        $this->assertNull($row['food_recipe_id']);
        $this->assertSame(0.15, $row['product_quantity']);
        $this->assertCount(2, json_decode($row['source_breakdown'], true));
        $this->assertArrayNotHasKey('_sources', $row);
    }

    public function testAddOnOnlyUsageDoesNotClaimAnOptionListId(): void
    {
        $method = new ReflectionMethod(OrderRecipeUsageController::class, 'finalizeUsageRow');
        $method->setAccessible(true);
        $controller = (new ReflectionClass(OrderRecipeUsageController::class))->newInstanceWithoutConstructor();
        $row = $method->invoke($controller, [
            'order_quantity' => 5.0,
            'product_quantity_used' => 0.5,
            'product_quantity' => 0.1,
            'option_list_id' => null,
            'food_recipe_id' => 11,
            '_sources' => ['addon:72:11' => [
                'type' => 'addon', 'target_id' => 72, 'food_recipe_id' => 11,
                'target_quantity' => 5.0, 'recipe_quantity' => 0.1, 'used_quantity' => 0.5,
            ]],
        ]);
        $this->assertNull($row['option_list_id']);
        $this->assertSame(11, $row['food_recipe_id']);
        $this->assertSame(0.1, $row['product_quantity']);
    }
}
