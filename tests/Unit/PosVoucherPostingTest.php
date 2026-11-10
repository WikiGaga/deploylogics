<?php

namespace Tests\Unit;

use App\Http\Controllers\Accounts\POSVoucherController;
use App\Library\PosVoucherPayment;
use App\Services\PosOrderVoucherService;
use Illuminate\Container\Container;
use Illuminate\Database\ConnectionResolver;
use Illuminate\Database\DatabaseManager;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\SQLiteConnection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Facade;
use Illuminate\Support\Facades\Validator;
use Mockery;
use PDO;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class PosVoucherPostingTest extends TestCase
{
    private $previousFacadeApplication;
    private $previousConnectionResolver;

    protected function setUp(): void
    {
        parent::setUp();
        if (!class_exists('Validator')) {
            class_alias(Validator::class, 'Validator');
        }
        $this->previousFacadeApplication = Facade::getFacadeApplication();
        $this->previousConnectionResolver = Model::getConnectionResolver();
        Facade::clearResolvedInstances();
        Facade::setFacadeApplication(new Container());
        DB::swap(Mockery::mock(DatabaseManager::class));

        // Only the accounting configuration uses Eloquent; all order reads are mocked.
        $connection = new SQLiteConnection(new PDO('sqlite::memory:'));
        $connection->statement('CREATE TABLE tbl_defi_configuration (
            configuration_id INTEGER PRIMARY KEY,
            sale_discount INTEGER, sale_income INTEGER, sale_vat_payable INTEGER,
            sale_cash_ac INTEGER, payment_receive_dr_ac INTEGER, payment_receive_cr_ac INTEGER,
            sale_return_discount INTEGER, sale_return_income INTEGER,
            sale_return_vat_payable INTEGER, sale_return_cash_ac INTEGER
        )');
        $connection->table('tbl_defi_configuration')->insert([
            'configuration_id' => 1,
            'sale_discount' => 4001,
            'sale_income' => 4002,
            'sale_vat_payable' => 4003,
            'sale_cash_ac' => 1001,
            'payment_receive_dr_ac' => 1002,
            'payment_receive_cr_ac' => 1003,
            'sale_return_discount' => 5001,
            'sale_return_income' => 5002,
            'sale_return_vat_payable' => 5003,
            'sale_return_cash_ac' => 1001,
        ]);
        $resolver = new ConnectionResolver(['voucher_test' => $connection]);
        $resolver->setDefaultConnection('voucher_test');
        Model::setConnectionResolver($resolver);
    }

    protected function tearDown(): void
    {
        try {
            Mockery::close();
        } finally {
            if ($this->previousConnectionResolver) {
                Model::setConnectionResolver($this->previousConnectionResolver);
            } else {
                Model::unsetConnectionResolver();
            }
            Facade::clearResolvedInstances();
            Facade::setFacadeApplication($this->previousFacadeApplication);
            parent::tearDown();
        }
    }

    /** @dataProvider partnerSalesCases */
    public function testPartnerSalesReachThePartnerAndConfiguredSalesAccounts($path, array $overrides)
    {
        $invoice = $this->invoice($overrides);
        $this->mockSummary($invoice);

        if ($path === 'manual') {
            $poster = $this->manualPoster($invoice);
            $response = $poster->store($this->postingRequest());
            $this->assertSame('success', $response['status'], $response['message']);
        } else {
            $poster = new CapturingPosOrderVoucherService();
            $response = $poster->syncOrder($invoice->order_id);
            $this->assertSame('posted', $response['action']);
            $this->assertSame([$invoice->order_id], $poster->deletedOrderIds);
        }

        $partnerLines = $this->linesForAccount($poster->lines, $invoice->partner_account_id);
        $salesLines = $this->linesForAccount($poster->lines, 4002);
        $this->assertCount(1, $partnerLines);
        $this->assertCount(1, $salesLines);
        $this->assertEqualsWithDelta($invoice->net_sales, $partnerLines[0]['voucher_debit'], 0.0001);
        $this->assertEquals(0, $partnerLines[0]['voucher_credit']);
        $this->assertEqualsWithDelta($invoice->items_amount, $salesLines[0]['voucher_credit'], 0.0001);
        $this->assertEquals(0, $salesLines[0]['voucher_debit']);
        $this->assertCount(0, $this->linesForAccount($poster->lines, 1001));
        $this->assertCount(0, $this->linesForAccount($poster->lines, $invoice->bank_account));
        $this->assertEqualsWithDelta(
            array_sum(array_column($poster->lines, 'voucher_debit')),
            array_sum(array_column($poster->lines, 'voucher_credit')),
            0.0001,
            'The posted partner voucher must balance.'
        );
        foreach ($poster->lines as $line) {
            $this->assertSame($invoice->order_id, $line['voucher_document_id']);
            $this->assertSame('POS', $line['voucher_type']);
        }
    }

    public function partnerSalesCases()
    {
        $cases = [
            'ordinary partner credit' => [],
            'credit after cash and card edit' => ['cash_sales' => 10, 'card_sales' => 11.5],
            'missing payment detail' => [
                'cash_sales' => null, 'card_sales' => null,
                'credit_sales' => null, 'delivery_partner_sales' => null,
            ],
            'zero payment detail' => ['delivery_partner_sales' => 0],
            'amount exceeding one thousand' => [
                'items_amount' => 1250, 'net_sales' => 1300.5,
                'delivery_partner_sales' => 1300.5, 'vat' => 50,
            ],
        ];
        foreach (['manual', 'automatic'] as $path) {
            foreach ($cases as $name => $overrides) {
                yield $path . ': ' . $name => [$path, $overrides];
            }
        }
    }

    /** @dataProvider postingPaths */
    public function testMissingPartnerAccountIsReportedWithoutPostingToAnotherAccount($path)
    {
        $invoice = $this->invoice(['partner_account_id' => null]);
        $this->mockSummary($invoice);

        if ($path === 'manual') {
            $poster = $this->manualPoster($invoice);
            $response = $poster->store($this->postingRequest());
            $this->assertSame('error', $response['status']);
            $this->assertStringContainsString('account', strtolower($response['message']));
        } else {
            $poster = new CapturingPosOrderVoucherService();
            $exception = null;
            try {
                $poster->syncOrder($invoice->order_id);
            } catch (RuntimeException $caught) {
                $exception = $caught;
            }
            $this->assertInstanceOf(RuntimeException::class, $exception, 'Missing delivery partner account must be reported.');
            $this->assertStringContainsString('account', strtolower($exception->getMessage()));
        }

        $this->assertSame([], $poster->lines);
    }

    public function postingPaths()
    {
        return [['manual'], ['automatic']];
    }

    public function testFullyDiscountedPartnerOrderKeepsTheManualZeroCashPosting()
    {
        $invoice = $this->invoice([
            'partner_account_id' => null,
            'delivery_partner_sales' => 0,
            'net_sales' => 0,
            'discount_on_items' => 20,
            'total_add_on_price' => 0,
            'order_total_discounts' => 0,
            'vat' => 0,
            'delivery_charges' => 0,
        ]);
        $this->mockSummary($invoice);
        $poster = $this->manualPoster($invoice);

        $response = $poster->store($this->postingRequest());

        $this->assertSame('success', $response['status'], $response['message']);
        $cashLines = $this->linesForAccount($poster->lines, 1001);
        $salesLines = $this->linesForAccount($poster->lines, 4002);
        $this->assertCount(1, $cashLines);
        $this->assertEquals(0, $cashLines[0]['voucher_debit']);
        $this->assertCount(1, $salesLines);
        $this->assertEquals(20, $salesLines[0]['voucher_credit']);
        $this->assertEquals(
            array_sum(array_column($poster->lines, 'voucher_debit')),
            array_sum(array_column($poster->lines, 'voucher_credit'))
        );
    }

    /** @dataProvider otherPaymentCases */
    public function testOtherPaymentMethodsKeepTheirExistingAccount($overrides, $expectedAccount, $expectedMethod)
    {
        $payment = PosVoucherPayment::resolve($this->invoice($overrides), 1001, 2003);

        $this->assertEquals($expectedAccount, $payment['account_id']);
        $this->assertSame($expectedMethod, $payment['method']);
    }

    public function otherPaymentCases()
    {
        return [
            'customer credit' => [[
                'partner_id' => null, 'partner_account_id' => null,
                'delivery_partner_sales' => 0, 'credit_sales' => 21.5,
                'customer_account_id' => 2002,
            ], 2002, 'credit'],
            'walk in credit fallback' => [[
                'partner_id' => null, 'partner_account_id' => null,
                'delivery_partner_sales' => 0, 'credit_sales' => 21.5,
            ], 2003, 'credit'],
            'partner order paid in cash' => [[
                'payment_method' => 'cash', 'delivery_partner_sales' => 0,
                'cash_sales' => 21.5,
            ], 1001, 'cash'],
            'partner order paid by card' => [[
                'payment_method' => 'card', 'delivery_partner_sales' => 0,
                'card_sales' => 21.5,
            ], 1004, 'card'],
            'partner order paid by cash and card' => [[
                'payment_method' => 'cash_card', 'delivery_partner_sales' => 0,
                'cash_sales' => 10, 'card_sales' => 11.5,
            ], 1004, 'cash_card'],
        ];
    }

    private function invoice(array $overrides)
    {
        return (object) array_merge([
            'order_id' => 12345,
            'order_serial' => '001-314-001',
            'order_date' => '2026-11-10',
            'branch_id' => 1,
            'business_id' => 1,
            'company_id' => 1,
            'payment_user_id' => 81,
            'payment_status' => 'paid',
            'order_status' => 'delivered',
            'sales_type' => 'POS',
            'payment_method' => 'credit',
            'partner_id' => 7001,
            'partner_name' => 'Delivery Partner',
            'partner_account_id' => 2001,
            'customer_id' => null,
            'customer_account_id' => null,
            'bank_account' => 1004,
            'cash_sales' => 0,
            'card_sales' => 0,
            'credit_sales' => 0,
            'delivery_partner_sales' => 21.5,
            'items_amount' => 20,
            'discount_on_items' => 2,
            'total_add_on_price' => 3,
            'order_total_discounts' => 1,
            'coupon_discount' => 0,
            'vat' => 1,
            'delivery_charges' => 0.5,
            'net_sales' => 21.5,
        ], $overrides);
    }

    private function mockSummary($invoice)
    {
        $query = Mockery::mock();
        $query->shouldReceive('where')->andReturnSelf();
        $query->shouldReceive('first')->once()->andReturn($invoice);
        DB::shouldReceive('table')->with('VW_REST_SUMMARY_ORDER_WISE')->once()->andReturn($query);
    }

    private function manualPoster($invoice)
    {
        $validator = Mockery::mock();
        $validator->shouldReceive('fails')->once()->andReturn(false);
        Validator::swap(Mockery::mock());
        Validator::shouldReceive('make')->once()->andReturn($validator);
        Auth::swap(Mockery::mock());
        Auth::shouldReceive('user')->andReturn((object) ['business_id' => 1, 'company_id' => 1, 'id' => 81]);
        DB::shouldReceive('beginTransaction')->once();
        DB::shouldReceive('commit')->zeroOrMoreTimes();
        DB::shouldReceive('rollBack')->zeroOrMoreTimes();
        DB::shouldReceive('delete')->times(3)->andReturn(0);
        DB::shouldReceive('select')->andReturnUsing(function ($sql) use ($invoice) {
            if (stripos($sql, 'select distinct ORDER_DATE, branch_id') !== false) {
                return [(object) ['order_date' => $invoice->order_date, 'branch_id' => $invoice->branch_id]];
            }
            if (strpos($sql, "SALES_TYPE = 'RPOS'") !== false) {
                return [];
            }
            if (strpos($sql, "SALES_TYPE = 'POS'") !== false) {
                return [(object) ['order_id' => $invoice->order_id]];
            }
            throw new RuntimeException('Unexpected voucher posting query: ' . $sql);
        });

        return new CapturingPosVoucherController();
    }

    private function postingRequest()
    {
        return Request::create('/pos-voucher', 'POST', [
            'pos_branch_ids' => [1],
            'date_from' => '10-11-2026',
            'date_to' => '10-11-2026',
            'pos_voucher' => 1,
        ]);
    }

    private function linesForAccount(array $lines, $accountId)
    {
        return array_values(array_filter($lines, static function ($line) use ($accountId) {
            return (string) $line['chart_account_id'] === (string) $accountId;
        }));
    }
}

class CapturingPosOrderVoucherService extends PosOrderVoucherService
{
    public $lines = [];
    public $deletedOrderIds = [];

    public function deleteOrderVouchers($orderId)
    {
        $this->deletedOrderIds[] = $orderId;
        return 0;
    }

    protected function insertVoucherLines($tableName, $data, $multiVoucher = false)
    {
        foreach ($multiVoucher ? $data : [$data] as $line) {
            $this->lines[] = $line;
        }
    }
}

class CapturingPosVoucherController extends POSVoucherController
{
    public $lines = [];

    public function __construct()
    {
    }

    public function proAccoVoucherInsert($id, $action, $tableName, $data, $whereClause, $multiVoucher = false)
    {
        foreach ($multiVoucher ? $data : [$data] as $line) {
            if (is_array($line)) {
                $this->lines[] = $line;
            }
        }
    }

    protected function jsonSuccessResponse($data, $message, $statusCode = 200)
    {
        return ['status' => 'success', 'message' => $message, 'data' => $data];
    }

    protected function jsonErrorResponse($data, $message, $statusCode = 200)
    {
        return ['status' => 'error', 'message' => $message, 'data' => $data];
    }
}
