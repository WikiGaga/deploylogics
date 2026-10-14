<?php

namespace Tests\Unit;

use App\Http\Controllers\Api\ApiHomeController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Tests\TestCase;

class AttendanceApiErrorTest extends TestCase
{
    private function execute($callback)
    {
        DB::shouldReceive('transaction')->once()->andReturnUsing(function ($operation) {
            return $operation();
        });
        Log::spy();
        $class = new \ReflectionClass(ApiHomeController::class);
        $method = $class->getMethod('attendanceTransaction');
        $method->setAccessible(true);
        return $method->invoke($class->newInstanceWithoutConstructor(), 'add_attendance', $callback);
    }

    public function testDatabaseFailureReturnsJsonWithoutLeakingExceptionDetails()
    {
        $response = $this->execute(function () {
            throw new \RuntimeException('internal database connection details');
        });
        $this->assertSame(500, $response->getStatusCode());
        $this->assertFalse($response->getData(true)['success']);
        $this->assertStringNotContainsString('internal database', $response->getContent());
        Log::shouldHaveReceived('error')->once();
    }

    public function testUnauthorizedBranchReturnsJson403()
    {
        $response = $this->execute(function () {
            throw new AccessDeniedHttpException();
        });
        $this->assertSame(403, $response->getStatusCode());
        $this->assertFalse($response->getData(true)['success']);
        Log::shouldHaveReceived('warning')->once();
    }

    public function testInvalidBranchReturnsValidationErrors()
    {
        $response = $this->execute(function () {
            throw ValidationException::withMessages(['branch_id' => 'Invalid attendance branch.']);
        });
        $this->assertSame(422, $response->getStatusCode());
        $this->assertArrayHasKey('branch_id', $response->getData(true)['errors']);
    }
}
