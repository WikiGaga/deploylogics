<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    // 1. Get Employee IDs
    $employees = DB::table('tbl_payr_employee')->select('EMPLOYEE_ID', 'EMPLOYEE_NAME')->get();
    
    if ($employees->isEmpty()) {
        echo "No employees found in tbl_payr_employee table!\n";
        exit;
    }

    echo "Found " . count($employees) . " employees.\n";

    // Get max ID from TBL_HR_ATTENDENCE_DTL
    $maxId = (int) DB::table('TBL_HR_ATTENDENCE_DTL')->max('ID');
    $nextId = $maxId + 1;

    $dates = [
        '2026-07-20',
        '2026-07-21',
        '2026-07-22',
        '2026-07-23',
        '2026-07-24'
    ];

    $insertedCount = 0;

    foreach ($employees as $empIndex => $emp) {
        $empId = $emp->employee_id ?? $emp->EMPLOYEE_ID;
        echo "Processing Employee: {$empId} - " . ($emp->employee_name ?? $emp->EMPLOYEE_NAME) . "\n";

        foreach ($dates as $dIndex => $dateStr) {
            // Scenario 1: Normal full day (Check-in 08:50 AM, Check-out 05:15 PM)
            // Scenario 2: Late Check-in (09:45 AM), Check-out (06:00 PM)
            // Scenario 3: Missing Check-Out (Only Check-in 09:00 AM)

            $scenario = ($empIndex + $dIndex) % 3;

            if ($scenario == 0 || $scenario == 1) {
                // Check-in
                $checkInTime = ($scenario == 1) ? "{$dateStr} 09:45:00" : "{$dateStr} 08:50:00";
                DB::table('TBL_HR_ATTENDENCE_DTL')->insert([
                    'ID' => $nextId++,
                    'EMP_ID' => $empId,
                    'ATTENDANCE_DATE' => DB::raw("TO_DATE('{$dateStr}', 'YYYY-MM-DD')"),
                    'ATTENDANCE_TIME' => DB::raw("TO_TIMESTAMP('{$checkInTime}', 'YYYY-MM-DD HH24:MI:SS')"),
                    'ATTENDANCE_TYPE' => 'check-in',
                    'IS_DELETED' => 0,
                    'BRANCH_ID' => 1,
                    'CREATED_AT' => now(),
                    'UPDATED_AT' => now()
                ]);
                $insertedCount++;

                // Check-out
                $checkOutTime = ($scenario == 1) ? "{$dateStr} 18:00:00" : "{$dateStr} 17:15:00";
                DB::table('TBL_HR_ATTENDENCE_DTL')->insert([
                    'ID' => $nextId++,
                    'EMP_ID' => $empId,
                    'ATTENDANCE_DATE' => DB::raw("TO_DATE('{$dateStr}', 'YYYY-MM-DD')"),
                    'ATTENDANCE_TIME' => DB::raw("TO_TIMESTAMP('{$checkOutTime}', 'YYYY-MM-DD HH24:MI:SS')"),
                    'ATTENDANCE_TYPE' => 'check-out',
                    'IS_DELETED' => 0,
                    'BRANCH_ID' => 1,
                    'CREATED_AT' => now(),
                    'UPDATED_AT' => now()
                ]);
                $insertedCount++;
            } else {
                // Missing Check-Out Scenario (Only Check-in)
                $checkInTime = "{$dateStr} 09:00:00";
                DB::table('TBL_HR_ATTENDENCE_DTL')->insert([
                    'ID' => $nextId++,
                    'EMP_ID' => $empId,
                    'ATTENDANCE_DATE' => DB::raw("TO_DATE('{$dateStr}', 'YYYY-MM-DD')"),
                    'ATTENDANCE_TIME' => DB::raw("TO_TIMESTAMP('{$checkInTime}', 'YYYY-MM-DD HH24:MI:SS')"),
                    'ATTENDANCE_TYPE' => 'check-in',
                    'IS_DELETED' => 0,
                    'BRANCH_ID' => 1,
                    'CREATED_AT' => now(),
                    'UPDATED_AT' => now()
                ]);
                $insertedCount++;
            }
        }
    }

    $msg = "Successfully inserted {$insertedCount} dummy attendance records!";
    echo $msg . "\n";
    file_put_contents('output.log', $msg);

} catch (\Exception $e) {
    $err = "Error: " . $e->getMessage();
    echo $err . "\n";
    file_put_contents('output.log', $err);
}
