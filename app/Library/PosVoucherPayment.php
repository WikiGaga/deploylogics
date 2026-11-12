<?php

namespace App\Library;

use RuntimeException;

class PosVoucherPayment
{
    public static function resolve($saleInvoice, $cashInHandAc, $walkInCustomerAc)
    {
        $cash = (float) ($saleInvoice->cash_sales ?? 0);
        $card = (float) ($saleInvoice->card_sales ?? 0);
        $credit = (float) ($saleInvoice->credit_sales ?? 0);
        $partner = (float) ($saleInvoice->delivery_partner_sales ?? 0);
        $paymentMethod = strtolower(trim((string) ($saleInvoice->payment_method ?? '')));
        $hasPartner = !empty($saleInvoice->partner_id)
            || !empty($saleInvoice->partner_account_id)
            || $partner > 0;

        // A credit checkout can retain cash/card amounts from an earlier payment.
        // The order's declared partner credit takes precedence over those details.
        if ($paymentMethod === 'credit' && $hasPartner) {
            return self::deliveryPartner($saleInvoice);
        }

        if ($cash > 0 && $card == 0 && $credit == 0 && $partner == 0) {
            return self::payment((int) $cashInHandAc, 'Cash', 'cash');
        }
        if ($cash == 0 && $card > 0 && $credit == 0 && $partner == 0) {
            return self::payment($saleInvoice->bank_account, 'Card', 'card');
        }
        if ($cash > 0 && $card > 0 && $credit == 0 && $partner == 0) {
            return self::payment($saleInvoice->bank_account, 'Cash and Card', 'cash_card');
        }
        if ($cash == 0 && $card == 0 && $credit > 0 && $partner == 0) {
            return self::payment($saleInvoice->customer_account_id ?? $walkInCustomerAc, 'Customer Credit - ' . ($saleInvoice->walk_customer_name ?? 'Walk-in Customer'), 'credit');
        }
        if ($cash == 0 && $card == 0 && $credit == 0 && $partner > 0) {
            return self::deliveryPartner($saleInvoice);
        }

        return self::payment('', '', '');
    }

    private static function deliveryPartner($saleInvoice)
    {
        if (empty($saleInvoice->partner_account_id)) {
            throw new RuntimeException('Delivery partner account is not configured for order ' . $saleInvoice->order_id);
        }

        return self::payment(
            $saleInvoice->partner_account_id,
            'Delivery Partner - ' . ($saleInvoice->partner_name ?? ''),
            'delivery_partner'
        );
    }

    private static function payment($accountId, $description, $method)
    {
        return ['account_id' => $accountId, 'description' => $description, 'method' => $method];
    }
}
