<?php

return [
    // menu_dtl_id => document primary key column (for staging / flow criteria lookups)
    'document_primary_keys_by_menu' => [
        '38' => 'purchase_order_id',   // Purchase Orders
        '354' => 'session_id',         // Shift Sessions
        '65' => 'stock_id',            // Stock Transfer
        '359' => 'stock_id',           // Stock Distribution
        '54' => 'stock_id',            // Opening Stock
        '76' => 'stock_id',            // Stock Receiving
        '57' => 'stock_id',            // Damaged Items
        '23' => 'grn_id',              // Goods Received Notes (GRN)
        '28' => 'voucher_id',          // Cash Receipt Voucher (CRV)
        '29' => 'voucher_id',          // Bank Receipt Voucher (BRV)
        '31' => 'voucher_id',          // Journal Voucher (JV)
        '36' => 'voucher_id',          // Bank Payment Voucher (BPV)
        '37' => 'voucher_id',          // Cash Payment Voucher (CPV)
        '62' => 'voucher_id',          // Opening Balance Voucher (OBV)
        '336' => 'code',               // Production & Consumption
    ],

    'voucher_type_by_menu' => [
        '28' => 'crv',
        '29' => 'brv',
        '31' => 'jv',
        '36' => 'bpv',
        '37' => 'cpv',
        '62' => 'obv',
        '138' => 'ctrv',
        '171' => 'lfv',
    ],
];
