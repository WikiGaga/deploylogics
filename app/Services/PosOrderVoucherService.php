<?php

namespace App\Services;

use App\Library\Utilities;
use App\Models\TblAccoVoucher;
use App\Models\TblDefiConfiguration;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Exception;

class PosOrderVoucherService
{
    public function syncOrder($orderId)
    {
        $saleInvoice = DB::table('VW_REST_SUMMARY_ORDER_WISE')
            ->where('order_id', $orderId)
            ->first();

        if (!$saleInvoice) {
            return [
                'order_id' => $orderId,
                'action' => 'skipped',
                'reason' => 'Order not found in VW_REST_SUMMARY_ORDER_WISE',
            ];
        }

        $this->deleteOrderVouchers($orderId);

        $salesType = strtoupper(trim((string) $saleInvoice->sales_type));
        $paymentStatus = strtolower(trim((string) ($saleInvoice->payment_status ?? '')));
        $orderStatus = strtolower(trim((string) ($saleInvoice->order_status ?? '')));

        if ($salesType === 'POS') {
            if ($paymentStatus !== 'paid' || $orderStatus === 'canceled') {
                return [
                    'order_id' => $orderId,
                    'action' => 'deleted',
                    'voucher_type' => 'POS',
                    'reason' => 'Order is not paid or is canceled',
                ];
            }

            $voucherId = $this->createPosVoucher($saleInvoice);
            if (!$voucherId) {
                return [
                    'order_id' => $orderId,
                    'action' => 'skipped',
                    'voucher_type' => 'POS',
                    'reason' => 'Unable to resolve payment account',
                ];
            }

            return [
                'order_id' => $orderId,
                'action' => 'posted',
                'voucher_type' => 'POS',
                'voucher_id' => $voucherId,
            ];
        }

        if ($salesType === 'RPOS') {
            $voucherId = $this->createRposVoucher($saleInvoice);
            if (!$voucherId) {
                return [
                    'order_id' => $orderId,
                    'action' => 'skipped',
                    'voucher_type' => 'RPOS',
                    'reason' => 'Unable to resolve payment account',
                ];
            }

            return [
                'order_id' => $orderId,
                'action' => 'posted',
                'voucher_type' => 'RPOS',
                'voucher_id' => $voucherId,
            ];
        }

        return [
            'order_id' => $orderId,
            'action' => 'skipped',
            'reason' => 'Unsupported sales type: ' . $salesType,
        ];
    }

    public function deleteOrderVouchers($orderId)
    {
        return TblAccoVoucher::where('voucher_document_id', $orderId)
            ->whereIn('voucher_type', ['POS', 'RPOS'])
            ->delete();
    }

    protected function createPosVoucher($saleInvoice)
    {
        $config = TblDefiConfiguration::first();
        if (!$config) {
            throw new Exception('Accounting configuration not found');
        }

        $discountChartAccountId = $config->sale_discount;
        $incomeChartAccountId = $config->sale_income;
        $vatPayableChartAccountId = $config->sale_vat_payable;
        $walkInCustomerAc = '13425120060615';
        $cashInHandAc = $config->sale_cash_ac;

        $ac = '';
        $descrip = '';
        $paymentMethod = '';

        if ($saleInvoice->cash_sales > 0 && $saleInvoice->card_sales == 0 && $saleInvoice->credit_sales == 0 && $saleInvoice->delivery_partner_sales == 0) {
            $ac = (int) $cashInHandAc;
            $descrip = 'Cash';
            $paymentMethod = 'cash';
        }
        if ($saleInvoice->cash_sales == 0 && $saleInvoice->card_sales > 0 && $saleInvoice->credit_sales == 0 && $saleInvoice->delivery_partner_sales == 0) {
            $ac = $saleInvoice->bank_account;
            $descrip = 'Card';
            $paymentMethod = 'card';
        }
        if ($saleInvoice->cash_sales > 0 && $saleInvoice->card_sales > 0 && $saleInvoice->credit_sales == 0 && $saleInvoice->delivery_partner_sales == 0) {
            $ac = $saleInvoice->bank_account;
            $descrip = 'Cash and Card';
            $paymentMethod = 'cash_card';
        }
        if ($saleInvoice->cash_sales == 0 && $saleInvoice->card_sales == 0 && $saleInvoice->credit_sales > 0 && $saleInvoice->delivery_partner_sales == 0) {
            $ac = $saleInvoice->customer_account_id ?? $walkInCustomerAc;
            $descrip = 'Customer Credit Credit';
            $paymentMethod = 'credit';
        }
        if ($saleInvoice->cash_sales == 0 && $saleInvoice->card_sales == 0 && $saleInvoice->credit_sales == 0 && $saleInvoice->delivery_partner_sales > 0) {
            $ac = $saleInvoice->partner_account_id;
            $descrip = 'Delivery Partner - ' . $saleInvoice->partner_name;
            $paymentMethod = 'delivery_partner';
        }

        if (empty($ac)) {
            return null;
        }

        $voucherId = Utilities::uuid();
        $date = date('Y-m-d', strtotime($saleInvoice->order_date));
        $netTotal = number_format((float) $saleInvoice->net_sales, 3);
        $tableName = 'tbl_acco_voucher';

        $data = [
            'voucher_id' => $voucherId,
            'voucher_document_id' => $saleInvoice->order_id,
            'voucher_no' => $saleInvoice->order_serial,
            'voucher_date' => $date,
            'voucher_descrip' => 'POS: ' . $descrip,
            'voucher_type' => 'POS',
            'posted' => 1,
            'branch_id' => $saleInvoice->branch_id,
            'business_id' => $saleInvoice->business_id,
            'company_id' => $saleInvoice->company_id,
            'voucher_user_id' => $saleInvoice->payment_user_id,
        ];

        if ($paymentMethod == 'cash_card' || $paymentMethod == 'cash_credit') {
            $voucherArray = [];

            if ($paymentMethod == 'cash_credit') {
                $subPartialAmount = $saleInvoice->credit_sales;
            } else {
                $subPartialAmount = $saleInvoice->card_sales;
            }

            $data['chart_account_id'] = (int) $cashInHandAc;
            $data['voucher_debit'] = abs($netTotal) - abs($subPartialAmount);
            $data['voucher_credit'] = 0;
            $data['voucher_sr_no'] = 0;
            $data['created_at'] = Carbon::now();
            $data['updated_at'] = Carbon::now();
            array_push($voucherArray, $data);

            $data['chart_account_id'] = $ac;
            $data['voucher_debit'] = abs($subPartialAmount);
            $data['voucher_credit'] = 0;
            $data['voucher_sr_no'] = 1;
            $data['created_at'] = Carbon::now();
            $data['updated_at'] = Carbon::now();
            array_push($voucherArray, $data);

            $this->insertVoucherLines($tableName, $voucherArray, true);
        } else {
            $data['chart_account_id'] = $ac;
            $data['voucher_debit'] = abs($netTotal);
            $data['voucher_credit'] = 0;
            $data['voucher_sr_no'] = 1;
            $this->insertVoucherLines($tableName, $data);
        }

        $data['chart_account_id'] = $incomeChartAccountId;
        $data['voucher_debit'] = 0;
        $data['voucher_credit'] = abs(number_format((float) $saleInvoice->items_amount, 3));
        $data['voucher_sr_no'] = 2;
        $this->insertVoucherLines($tableName, $data);

        $data['chart_account_id'] = $discountChartAccountId;
        $data['voucher_debit'] = abs(number_format((float) $saleInvoice->discount_on_items, 3));
        $data['voucher_credit'] = 0;
        $data['voucher_sr_no'] = 3;
        $this->insertVoucherLines($tableName, $data);

        $data['chart_account_id'] = 65981926021315;
        $data['voucher_debit'] = 0;
        $data['voucher_credit'] = abs(number_format((float) $saleInvoice->total_add_on_price, 3));
        $data['voucher_sr_no'] = 4;
        $this->insertVoucherLines($tableName, $data);

        $data['chart_account_id'] = 11916726021355;
        $data['voucher_debit'] = (abs(number_format((float) $saleInvoice->order_total_discounts, 3)) + abs(number_format((float) $saleInvoice->coupon_discount, 3)));
        $data['voucher_credit'] = 0;
        $data['voucher_sr_no'] = 5;
        $this->insertVoucherLines($tableName, $data);

        $data['chart_account_id'] = $vatPayableChartAccountId;
        $data['voucher_debit'] = 0;
        $data['voucher_credit'] = abs(number_format((float) $saleInvoice->vat, 3));
        $data['voucher_sr_no'] = 6;
        $this->insertVoucherLines($tableName, $data);

        $data['chart_account_id'] = 58141426181811;
        $data['voucher_debit'] = 0;
        $data['voucher_credit'] = abs(number_format((float) $saleInvoice->delivery_charges, 3));
        $data['voucher_sr_no'] = 7;
        $this->insertVoucherLines($tableName, $data);

        return $voucherId;
    }

    protected function createRposVoucher($saleInvoice)
    {
        $config = TblDefiConfiguration::first();
        if (!$config) {
            throw new Exception('Accounting configuration not found');
        }

        $saleReturnDiscountAc = $config->sale_return_discount;
        $saleReturnIncomeAc = $config->sale_return_income;
        $saleReturnVatPayableAc = $config->sale_return_vat_payable;
        $cashInHandAc = $config->sale_cash_ac;

        $ac = '';
        $descrip = '';

        if ($saleInvoice->payment_method == 'cash') {
            $ac = (int) $cashInHandAc;
            $descrip = 'Cash';
        }
        if ($saleInvoice->payment_method == 'cash_card' || $saleInvoice->payment_method == 'card') {
            $ac = $saleInvoice->bank_account;
            $descrip = 'Visa or (Cash and Visa)';
        }
        if ($saleInvoice->payment_method == 'credit' || $saleInvoice->payment_method == 'cash_credit') {
            $descrip = 'Credit or (Cash and Credit)';
            $partner = DB::table('tbl_sale_order_partners')
                ->where('partner_id', $saleInvoice->partner_id)
                ->first();

            if ($partner) {
                $ac = (int) $partner->partner_account_id;
                $descrip = 'Credit - ' . $partner->partner_name;
            } else {
                $customer = DB::table('tbl_sale_customer')
                    ->where('customer_id', $saleInvoice->customer_id)
                    ->first();

                if ($customer) {
                    $ac = (int) $customer->customer_account_id;
                    $descrip = 'Credit - ' . $customer->customer_name;
                }
            }
        }

        if (empty($ac)) {
            return null;
        }

        $voucherId = Utilities::uuid();
        $date = date('Y-m-d', strtotime($saleInvoice->order_date));
        $netTotal = number_format((float) $saleInvoice->net_sales, 3);
        $tableName = 'tbl_acco_voucher';

        $data = [
            'voucher_id' => $voucherId,
            'voucher_document_id' => $saleInvoice->order_id,
            'voucher_no' => $saleInvoice->order_serial,
            'voucher_date' => $date,
            'voucher_descrip' => 'RPOS: ' . $descrip,
            'voucher_type' => 'RPOS',
            'posted' => 1,
            'branch_id' => $saleInvoice->branch_id,
            'business_id' => $saleInvoice->business_id,
            'company_id' => $saleInvoice->company_id,
            'voucher_user_id' => $saleInvoice->payment_user_id,
        ];

        if ($saleInvoice->payment_method == 'cash_card' || $saleInvoice->payment_method == 'cash_credit') {
            $voucherArray = [];
            if ($saleInvoice->payment_method == 'cash_credit') {
                $subPartialAmount = $saleInvoice->credit_sales;
            } else {
                $subPartialAmount = $saleInvoice->card_sales;
            }

            $data['chart_account_id'] = (int) $cashInHandAc;
            $data['voucher_debit'] = 0;
            $data['voucher_credit'] = abs($netTotal) - abs($subPartialAmount);
            $data['voucher_sr_no'] = 0;
            $data['created_at'] = Carbon::now();
            $data['updated_at'] = Carbon::now();
            array_push($voucherArray, $data);

            $data['chart_account_id'] = $ac;
            $data['voucher_debit'] = 0;
            $data['voucher_credit'] = abs($subPartialAmount);
            $data['voucher_sr_no'] = 1;
            $data['created_at'] = Carbon::now();
            $data['updated_at'] = Carbon::now();
            array_push($voucherArray, $data);

            $this->insertVoucherLines($tableName, $voucherArray, true);
        } else {
            $data['chart_account_id'] = $ac;
            $data['voucher_debit'] = 0;
            $data['voucher_credit'] = abs($netTotal);
            $data['voucher_sr_no'] = 1;
            $this->insertVoucherLines($tableName, $data);
        }

        $data['chart_account_id'] = $saleReturnIncomeAc;
        $data['voucher_debit'] = (abs(number_format((float) $saleInvoice->item_total, 3)));
        $data['voucher_credit'] = 0;
        $data['voucher_sr_no'] = 3;
        $this->insertVoucherLines($tableName, $data);

        $data['chart_account_id'] = $saleReturnDiscountAc;
        $data['voucher_debit'] = 0;
        $data['voucher_credit'] = abs(number_format((float) $saleInvoice->discount_on_items, 3));
        $data['voucher_sr_no'] = 2;
        $this->insertVoucherLines($tableName, $data);

        $data['chart_account_id'] = 65981926021315;
        $data['voucher_debit'] = abs(number_format((float) $saleInvoice->total_add_on_price, 3));
        $data['voucher_credit'] = 0;
        $data['voucher_sr_no'] = 3;
        $this->insertVoucherLines($tableName, $data);

        $data['chart_account_id'] = 11916726021355;
        $data['voucher_debit'] = 0;
        $data['voucher_credit'] = (abs(number_format((float) $saleInvoice->order_total_discounts, 3)) + abs(number_format((float) $saleInvoice->coupon_discount, 3)));
        $data['voucher_sr_no'] = 4;
        $this->insertVoucherLines($tableName, $data);

        $data['chart_account_id'] = $saleReturnVatPayableAc;
        $data['voucher_debit'] = abs(number_format((float) $saleInvoice->vat, 3));
        $data['voucher_credit'] = 0;
        $data['voucher_sr_no'] = 4;
        $this->insertVoucherLines($tableName, $data);

        return $voucherId;
    }

    protected function insertVoucherLines($tableName, $data, $multiVoucher = false)
    {
        if ($multiVoucher) {
            foreach ($data as $voucher) {
                DB::table($tableName)->insert($voucher);
            }
            return;
        }

        $data['created_at'] = Carbon::now();
        $data['updated_at'] = Carbon::now();
        DB::table($tableName)->insert($data);
    }
}
