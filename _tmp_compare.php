<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== tbl_defi_payment_type ===\n";
foreach (DB::table('tbl_defi_payment_type')->get() as $r) {
    echo $r->payment_type_id . ' | ' . $r->payment_type_name . "\n";
}

echo "\n=== tbl_acco_payment_mode (sample) ===\n";
foreach (DB::table('tbl_acco_payment_mode')->get() as $r) {
    echo $r->payment_mode_id . ' | ' . $r->payment_mode_name . "\n";
}
