<?php

namespace App\Library;

use DateTimeImmutable;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AttendancePersistence
{
    public static function fingerprint($row)
    {
        $values = (array) $row;
        ksort($values);
        foreach ($values as $key => $value) {
            $values[$key] = $value === null ? null : (string) $value;
        }
        if (isset($values['attendance_type'])) {
            $values['attendance_type'] = strtolower(str_replace(['-', ' '], '_', trim($values['attendance_type'])));
        }
        return hash('sha256', json_encode($values));
    }

    // All attendance writers lock the same existing branch row before checking
    // headers or retry identities. The lock is held by the caller's transaction.
    public static function lockBranch($businessId, $companyId, $branchId)
    {
        $branch = DB::table('tbl_soft_branch')->where('branch_id', $branchId)
            ->where('business_id', $businessId)->where('company_id', $companyId)
            ->lockForUpdate()->first();
        if (!$branch) {
            throw ValidationException::withMessages(['branch_id' => 'Invalid attendance branch.']);
        }
    }

    public static function id($table)
    {
        do {
            $id = (string) random_int(10000000000000, 99999999999999);
        } while (DB::table($table)->where('id', $id)->exists());
        return $id;
    }

    public static function time($date, $time)
    {
        foreach (['h:i A', 'g:i A', 'H:i', 'H:i:s'] as $format) {
            $parsed = DateTimeImmutable::createFromFormat('!' . $format, trim((string) $time));
            $errors = DateTimeImmutable::getLastErrors();
            if ($parsed && (!$errors || (!$errors['warning_count'] && !$errors['error_count']))) {
                return $date . ' ' . $parsed->format('H:i:s');
            }
        }
        throw ValidationException::withMessages(['attendance_time' => 'Please select a valid attendance time.']);
    }
}
