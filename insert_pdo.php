<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$tns = "(DESCRIPTION = (ADDRESS = (PROTOCOL = TCP)(HOST = 127.0.0.1)(PORT = 1521)) (CONNECT_DATA = (SERVICE_NAME = ORCL)))";
$db_username = "MALEKALPIZZA_LOCAL";
$db_password = "usama1122";

try {
    $pdo = new PDO("oci:dbname=" . $tns, $db_username, $db_password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
    echo "Connected to Oracle DB successfully!\n";

    // 1. Fetch employees
    $stmt = $pdo->query("SELECT EMPLOYEE_ID, EMPLOYEE_NAME FROM tbl_payr_employee WHERE ROWNUM <= 20");
    $employees = $stmt->fetchAll();

    echo "Found " . count($employees) . " employees.\n";

    if (empty($employees)) {
        echo "No employees in tbl_payr_employee.\n";
        exit;
    }

    // Get max ID
    $maxStmt = $pdo->query("SELECT NVL(MAX(ID), 0) AS MAX_ID FROM TBL_HR_ATTENDENCE_DTL");
    $row = $maxStmt->fetch();
    $nextId = (int)$row['MAX_ID'] + 1;

    $dates = ['2026-07-20', '2026-07-21', '2026-07-22', '2026-07-23', '2026-07-24'];
    $inserted = 0;

    $insertSql = "INSERT INTO TBL_HR_ATTENDENCE_DTL 
        (ID, EMP_ID, ATTENDANCE_DATE, ATTENDANCE_TIME, ATTENDANCE_TYPE, IS_DELETED, BRANCH_ID, CREATED_AT, UPDATED_AT) 
        VALUES (:id, :emp_id, TO_DATE(:att_date, 'YYYY-MM-DD'), TO_TIMESTAMP(:att_time, 'YYYY-MM-DD HH24:MI:SS'), :att_type, 0, 1, SYSDATE, SYSDATE)";

    $insertStmt = $pdo->prepare($insertSql);

    foreach ($employees as $idx => $emp) {
        $empId = $emp['EMPLOYEE_ID'];
        $empName = $emp['EMPLOYEE_NAME'];
        echo "Processing {$empId} - {$empName}...\n";

        foreach ($dates as $dIdx => $dateStr) {
            $scenario = ($idx + $dIdx) % 3;

            if ($scenario == 0 || $scenario == 1) {
                // Check-in
                $checkInTime = ($scenario == 1) ? "{$dateStr} 09:45:00" : "{$dateStr} 08:50:00";
                $insertStmt->execute([
                    ':id' => $nextId++,
                    ':emp_id' => $empId,
                    ':att_date' => $dateStr,
                    ':att_time' => $checkInTime,
                    ':att_type' => 'check-in'
                ]);
                $inserted++;

                // Check-out
                $checkOutTime = ($scenario == 1) ? "{$dateStr} 18:00:00" : "{$dateStr} 17:15:00";
                $insertStmt->execute([
                    ':id' => $nextId++,
                    ':emp_id' => $empId,
                    ':att_date' => $dateStr,
                    ':att_time' => $checkOutTime,
                    ':att_type' => 'check-out'
                ]);
                $inserted++;
            } else {
                // Incomplete Punch (Check-in only)
                $checkInTime = "{$dateStr} 09:00:00";
                $insertStmt->execute([
                    ':id' => $nextId++,
                    ':emp_id' => $empId,
                    ':att_date' => $dateStr,
                    ':att_time' => $checkInTime,
                    ':att_type' => 'check-in'
                ]);
                $inserted++;
            }
        }
    }

    echo "\n=== SUCCESS! Inserted {$inserted} attendance records into TBL_HR_ATTENDENCE_DTL. ===\n";

} catch (PDOException $e) {
    echo "PDO Exception: " . $e->getMessage() . "\n";
} catch (Exception $e) {
    echo "Exception: " . $e->getMessage() . "\n";
}
