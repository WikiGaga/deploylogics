<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$cols = DB::select("SELECT column_name FROM user_tab_columns WHERE table_name = 'TBL_PURC_GRN' AND column_name IN ('BANK_ID','PAYMENT_ACCOUNT_ID','PAYMENT_TYPE_ID')");
foreach ($cols as $c) {
    echo "HAS: " . $c->column_name . PHP_EOL;
}

$names = array_map(function ($c) { return strtoupper($c->column_name); }, $cols);

if (in_array('BANK_ID', $names) && !in_array('PAYMENT_ACCOUNT_ID', $names)) {
    DB::statement('ALTER TABLE tbl_purc_grn RENAME COLUMN bank_id TO payment_account_id');
    echo "RENAMED bank_id -> payment_account_id\n";
} elseif (!in_array('PAYMENT_ACCOUNT_ID', $names)) {
    DB::statement('ALTER TABLE tbl_purc_grn ADD payment_account_id NUMBER NULL');
    echo "ADDED payment_account_id\n";
} else {
    echo "payment_account_id already exists\n";
}

if (in_array('BANK_ID', $names) && in_array('PAYMENT_ACCOUNT_ID', $names)) {
    DB::statement('ALTER TABLE tbl_purc_grn DROP COLUMN bank_id');
    echo "DROPPED bank_id\n";
}
