<?php

namespace Tests\Unit;

use App\Library\AttendancePersistence;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class AttendancePersistenceTest extends TestCase
{
    public function testTimeUsesHeaderDateAndHandlesNoonAndMidnight()
    {
        $this->assertSame('2026-10-13 00:00:00', AttendancePersistence::time('2026-10-13', '12:00 AM'));
        $this->assertSame('2026-10-13 12:00:00', AttendancePersistence::time('2026-10-13', '12:00 PM'));
        $this->assertSame('2026-10-13 16:16:21', AttendancePersistence::time('2026-10-13', '16:16:21'));
        $this->assertSame('2026-10-13 13:30:00', AttendancePersistence::time('2026-10-13', '01:30 PM'));
    }

    public function testFingerprintToleratesLegacyTypeAndDatabaseIdTypes()
    {
        $this->assertSame(
            AttendancePersistence::fingerprint(['id' => 12, 'attendance_type' => ' Check-In ']),
            AttendancePersistence::fingerprint(['attendance_type' => 'check_in', 'id' => '12'])
        );
        $this->assertNotSame(
            AttendancePersistence::fingerprint(['attendance_time' => '2026-10-13 16:16:21']),
            AttendancePersistence::fingerprint(['attendance_time' => '2026-10-13 16:16:22'])
        );
    }

    public function testRejectsRelativeTime()
    {
        $this->expectException(ValidationException::class);
        AttendancePersistence::time('2026-10-13', 'tomorrow');
    }
}
