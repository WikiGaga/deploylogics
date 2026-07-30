<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

$out = [];
try {
    $employees = DB::table('tbl_payr_employee')->get();
    $out[] = "Employee count: " . count($employees);
    if (count($employees) > 0) {
        $first = (array) $employees[0];
        $out[] = "Employee 0 keys: " . implode(', ', array_keys($first));
    }
} catch (\Exception $e) {
    $out[] = "Exception: " . $e->getMessage();
}

file_put_contents(__DIR__ . '/debug_emp.txt', implode("\n", $out));
