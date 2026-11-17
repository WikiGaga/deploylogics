<?php

namespace Tests\Unit;

use App\Http\Controllers\Setting\POSDiscountController;
use App\Models\TblPosDiscountType;
use Illuminate\Auth\GenericUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class PosDiscountTypeTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // All persistence checks use an isolated database, never the configured Oracle DB.
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:']);
        DB::purge('sqlite');
        require_once database_path('migrations/2026_06_02_141751_create_tbl_pos_discount_types.php');
        require_once database_path('migrations/2026_11_17_000001_add_order_discount_fields_to_tbl_pos_discount_types.php');
        (new \CreateTblPosDiscountTypes())->up();
        (new \AddOrderDiscountFieldsToTblPosDiscountTypes())->up();

        auth()->guard()->setUser(new GenericUser([
            'id' => 1, 'business_id' => 10, 'company_id' => 20, 'branch_id' => 30,
        ]));
    }

    public function testExistingDiscountsRemainPerProductAfterMigration()
    {
        Schema::drop('tbl_pos_discount_types');
        (new \CreateTblPosDiscountTypes())->up();
        DB::table('tbl_pos_discount_types')->insert([
            'name' => 'Existing discount', 'default_discount' => 15,
            'business_id' => 10, 'company_id' => 20,
        ]);

        $migration = new \AddOrderDiscountFieldsToTblPosDiscountTypes();
        $migration->up();
        $discount = TblPosDiscountType::first();
        $this->assertFalse($discount->is_order_discount);
        $this->assertNull($discount->max_discount);
        $this->assertEquals(15, $discount->default_discount);

        // Re-running a partially applied Oracle DDL migration must be safe.
        $migration->up();
        $this->assertSame(1, TblPosDiscountType::count());
    }

    public function testLegacyRequestsStillSavePerProductDiscounts()
    {
        $response = $this->save();
        $this->assertSame('success', $response->getData(true)['status']);
        $discount = TblPosDiscountType::first();
        $this->assertFalse($discount->is_order_discount);
        $this->assertNull($discount->max_discount);
        $this->assertSame(10, (int) $discount->business_id);
        $this->assertSame(20, (int) $discount->company_id);
        $this->assertSame('/discount-types/form/'.$discount->id, $response->getData(true)['data']['redirect']);
    }

    public function testOverallBillDiscountSavesMaximumAmountAndReloadsIt()
    {
        $response = $this->save(['is_order_discount' => '1', 'max_discount' => '5.125']);
        $this->assertSame('success', $response->getData(true)['status']);
        $discount = TblPosDiscountType::first();
        $this->assertTrue($discount->is_order_discount);
        $this->assertSame('5.125', $discount->max_discount);

        $view = (new POSDiscountController())->create(new Request(), $discount->id);
        $current = $view->getData()['data']['current'];
        $this->assertTrue($current->is_order_discount);
        $this->assertSame('5.125', $current->max_discount);
    }

    public function testEditingCanSwitchScopesAndClearTheOldMaximum()
    {
        $this->save();
        $id = TblPosDiscountType::first()->id;
        $response = $this->save(['is_order_discount' => '1', 'max_discount' => '7.500'], $id);
        $this->assertSame('success', $response->getData(true)['status']);
        $this->assertTrue(TblPosDiscountType::find($id)->is_order_discount);

        $response = $this->save(['is_order_discount' => '0', 'max_discount' => '7.500'], $id);
        $this->assertSame('success', $response->getData(true)['status']);
        $discount = TblPosDiscountType::find($id);
        $this->assertFalse($discount->is_order_discount);
        $this->assertNull($discount->max_discount);
    }

    /** @dataProvider invalidOverallMaximums */
    public function testOverallBillRejectsInvalidMaximums($maximum)
    {
        $response = $this->save(['is_order_discount' => '1', 'max_discount' => $maximum]);
        $this->assertSame(422, $response->getStatusCode());
        $this->assertArrayHasKey('max_discount', $response->getData(true)['data']['validator_errors']);
        $this->assertSame(0, TblPosDiscountType::count());
    }

    public function invalidOverallMaximums()
    {
        return [[null], [''], ['not a number'], ['-0.001'], ['1000000000000']];
    }

    public function testOverallBillRequiresMaximumField()
    {
        $response = $this->save(['is_order_discount' => '1']);
        $this->assertSame(422, $response->getStatusCode());
        $this->assertSame(0, TblPosDiscountType::count());
    }

    public function testMaximumAmountMayExceedOneHundred()
    {
        $response = $this->save(['is_order_discount' => '1', 'max_discount' => '150.250']);
        $this->assertSame('success', $response->getData(true)['status']);
        $this->assertSame('150.250', TblPosDiscountType::first()->max_discount);
    }

    public function testInvalidScopeIsRejected()
    {
        $response = $this->save(['is_order_discount' => 'unexpected']);
        $this->assertSame(422, $response->getStatusCode());
        $this->assertArrayHasKey('is_order_discount', $response->getData(true)['data']['validator_errors']);
        $this->assertSame(0, TblPosDiscountType::count());
    }

    public function testInvalidEditPreservesSavedScopeAndMaximum()
    {
        $this->save(['is_order_discount' => '1', 'max_discount' => '5']);
        $id = TblPosDiscountType::first()->id;
        $response = $this->save(['is_order_discount' => '1', 'max_discount' => '-1'], $id);
        $this->assertSame(422, $response->getStatusCode());
        $discount = TblPosDiscountType::find($id);
        $this->assertTrue($discount->is_order_discount);
        $this->assertSame('5.000', $discount->max_discount);
    }

    public function testZeroMaximumIsAValidAmount()
    {
        $response = $this->save(['is_order_discount' => '1', 'max_discount' => '0']);
        $this->assertSame('success', $response->getData(true)['status']);
        $this->assertSame('0.000', TblPosDiscountType::first()->max_discount);
    }

    public function testPerProductIgnoresHiddenMaximumInput()
    {
        $response = $this->save(['is_order_discount' => '0', 'max_discount' => 'unused']);
        $this->assertSame('success', $response->getData(true)['status']);
        $this->assertNull(TblPosDiscountType::first()->max_discount);
    }

    public function testMigrationCanBeRolledBackAndAppliedAgain()
    {
        $this->save();
        $migration = new \AddOrderDiscountFieldsToTblPosDiscountTypes();
        $migration->down();
        $this->assertFalse(Schema::hasColumn('tbl_pos_discount_types', 'is_order_discount'));
        $this->assertFalse(Schema::hasColumn('tbl_pos_discount_types', 'max_discount'));
        $this->assertSame(1, TblPosDiscountType::count());
        $migration->up();
        $this->assertFalse(TblPosDiscountType::first()->is_order_discount);
    }

    public function testFormTemplateCompilesToValidPhp()
    {
        $compiled = Blade::compileString(file_get_contents(resource_path('views/setting/discount-types/form.blade.php')));
        $this->assertNotEmpty(token_get_all($compiled, TOKEN_PARSE));
    }

    private function save(array $overrides = [], $id = null)
    {
        $request = Request::create('/discount-types/form', 'POST', array_merge([
            'name' => 'Test discount', 'default_discount' => '20', 'status' => 'on',
        ], $overrides));

        return (new POSDiscountController())->store($request, $id);
    }
}
