<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class ActivityLogController extends Controller
{
    /**
     * Allowed values for log_type enum.
     */
    protected array $allowedLogTypes = [
        'api_error',
        'api_info',
        'face_registration',
        'face_attendance',
        'attendance_location',
        'attendance_api',
        'attendance_sync',
        'app_error',
        'info',
    ];

    /**
     * Bulk store activity logs from mobile app.
     *
     * POST /api/activity-logs/bulk
     */
    public function storeBulk(Request $request)
    {
        // 1. Batch size and array presence check
        $logs = $request->input('logs');

        if (!is_array($logs) || empty($logs)) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Invalid payload',
                'errors'  => [
                    'logs' => ['The logs field is required and must be a non-empty array.'],
                ],
            ], 400);
        }

        if (count($logs) > 200) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Batch too large',
            ], 413);
        }

        // 2. Resolve authentication context & token user
        $authUser   = auth('api')->user() ?? auth()->user() ?? $request->user();
        $authUserId = $authUser ? (string) ($authUser->id ?? $authUser->user_id) : null;
        $businessId = $authUser ? ($authUser->business_id ?? 1) : $request->input('business_id', 1);

        $deviceId   = $request->input('device_id');
        $appVersion = $request->input('app_version');
        $platform   = $request->input('platform');

        $acceptedClientIds  = [];
        $duplicateClientIds = [];
        $rejected           = [];

        $now = Carbon::now();

        foreach ($logs as $index => $item) {
            if (!is_array($item)) {
                $rejected[] = [
                    'client_log_id' => 0,
                    'reason'        => 'Log entry at index ' . $index . ' is not a valid object',
                ];
                continue;
            }

            $clientLogId = isset($item['client_log_id']) ? (int) $item['client_log_id'] : null;

            if ($clientLogId === null) {
                $rejected[] = [
                    'client_log_id' => 0,
                    'reason'        => 'Missing client_log_id',
                ];
                continue;
            }

            // Validate log_type
            $logType = $item['log_type'] ?? null;
            if (!$logType || !in_array($logType, $this->allowedLogTypes, true)) {
                $rejected[] = [
                    'client_log_id' => $clientLogId,
                    'reason'        => 'Invalid log_type: ' . ($logType ?? 'null'),
                ];
                continue;
            }

            // Validate created_at ISO-8601 timestamp
            $createdAtRaw = $item['created_at'] ?? null;
            if (!$createdAtRaw) {
                $rejected[] = [
                    'client_log_id' => $clientLogId,
                    'reason'        => 'Missing created_at timestamp',
                ];
                continue;
            }

            try {
                $eventAt = Carbon::parse($createdAtRaw);
            } catch (Throwable $e) {
                $rejected[] = [
                    'client_log_id' => $clientLogId,
                    'reason'        => 'Invalid created_at ISO-8601 format',
                ];
                continue;
            }

            // Reject rows more than 30 days old or > 5 minutes in future
            if ($eventAt->lt($now->copy()->subDays(30))) {
                $rejected[] = [
                    'client_log_id' => $clientLogId,
                    'reason'        => 'created_at is older than 30 days',
                ];
                continue;
            }

            if ($eventAt->gt($now->copy()->addMinutes(5))) {
                $rejected[] = [
                    'client_log_id' => $clientLogId,
                    'reason'        => 'created_at is in the future by more than 5 minutes',
                ];
                continue;
            }

            // Rule #7: Enforce user_id from token if available, otherwise payload user_id
            $userId = $authUserId ?? (isset($item['user_id']) ? (string) $item['user_id'] : null);
            $branchId = (isset($item['branch_id']) && $item['branch_id'] !== null)
                ? (int) $item['branch_id']
                : ($authUser ? ($authUser->branch_id ?? null) : null);

            // Truncate stack_trace if larger than 32 KB (32,768 characters)
            $stackTrace = $item['stack_trace'] ?? null;
            if (is_string($stackTrace) && mb_strlen($stackTrace) > 32768) {
                $stackTrace = mb_substr($stackTrace, 0, 32768);
            }

            // Format extra_data (valid JSON, max ~8 KB)
            $extraData = $item['extra_data'] ?? null;
            if (is_string($extraData)) {
                $decoded = json_decode($extraData, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $extraData = $decoded;
                }
            }

            // Deduplication Check: (user_id, device_id, client_log_id)
            $existing = ActivityLog::where('user_id', $userId)
                ->where('device_id', $deviceId)
                ->where('client_log_id', $clientLogId)
                ->first();

            if ($existing) {
                $duplicateClientIds[] = $clientLogId;
                continue;
            }

            try {
                ActivityLog::create([
                    'business_id'   => $businessId,
                    'user_id'       => $userId,
                    'branch_id'     => $branchId,
                    'device_id'     => $deviceId,
                    'app_version'   => $appVersion,
                    'platform'      => $platform,
                    'client_log_id' => $clientLogId,
                    'log_type'      => $logType,
                    'category'      => $item['category'] ?? null,
                    'source'        => $item['source'] ?? null,
                    'message'       => $item['message'] ?? null,
                    'error_details' => $item['error_details'] ?? null,
                    'stack_trace'   => $stackTrace,
                    'status_code'   => isset($item['status_code']) ? (int) $item['status_code'] : null,
                    'extra_data'    => $extraData,
                    'event_at'      => $eventAt,
                    'received_at'   => $now,
                ]);

                $acceptedClientIds[] = $clientLogId;
            } catch (QueryException $e) {
                $errorMessage = $e->getMessage();
                // If unique constraint violation occurs during race condition
                if (
                    str_contains($errorMessage, 'uq_') ||
                    str_contains($errorMessage, 'UNIQUE') ||
                    str_contains($errorMessage, '23000') ||
                    $e->getCode() == 23000 ||
                    $e->getCode() == 1
                ) {
                    $duplicateClientIds[] = $clientLogId;
                } else {
                    Log::error('Failed to insert activity log', [
                        'client_log_id' => $clientLogId,
                        'error'         => $errorMessage,
                    ]);
                    $rejected[] = [
                        'client_log_id' => $clientLogId,
                        'reason'        => 'Database insert failed: ' . $errorMessage,
                    ];
                }
            } catch (Throwable $e) {
                Log::error('Failed to process activity log item', [
                    'client_log_id' => $clientLogId,
                    'error'         => $e->getMessage(),
                ]);
                $rejected[] = [
                    'client_log_id' => $clientLogId,
                    'reason'        => 'Internal error: ' . $e->getMessage(),
                ];
            }
        }

        $received = count($logs);
        $stored   = count($acceptedClientIds);

        return response()->json([
            'status'               => 'success',
            'message'              => 'Logs received',
            'received'             => $received,
            'stored'               => $stored,
            'accepted_client_ids'  => array_values(array_unique($acceptedClientIds)),
            'duplicate_client_ids' => array_values(array_unique($duplicateClientIds)),
            'rejected'             => $rejected,
        ], 200);
    }

    /**
     * Admin dashboard list with filters and pagination.
     * GET /api/activity-logs
     */
    public function index(Request $request)
    {
        $query = ActivityLog::query();

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->input('user_id'));
        }

        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->input('branch_id'));
        }

        if ($request->filled('log_type')) {
            $query->where('log_type', $request->input('log_type'));
        }

        if ($request->filled('date_from')) {
            $query->where('event_at', '>=', Carbon::parse($request->input('date_from'))->startOfDay());
        }

        if ($request->filled('date_to')) {
            $query->where('event_at', '<=', Carbon::parse($request->input('date_to'))->endOfDay());
        }

        $perPage = (int) $request->input('per_page', 20);
        $logs = $query->orderBy('event_at', 'desc')->paginate($perPage);

        return response()->json([
            'status' => 'success',
            'data'   => $logs,
        ], 200);
    }

    /**
     * Summary counts grouped by log_type / category for date range.
     * GET /api/activity-logs/summary
     */
    public function summary(Request $request)
    {
        $query = ActivityLog::query();

        if ($request->filled('date_from')) {
            $query->where('event_at', '>=', Carbon::parse($request->input('date_from'))->startOfDay());
        }

        if ($request->filled('date_to')) {
            $query->where('event_at', '<=', Carbon::parse($request->input('date_to'))->endOfDay());
        }

        $byType = (clone $query)
            ->selectRaw('log_type, COUNT(*) as count')
            ->groupBy('log_type')
            ->get();

        $byCategory = (clone $query)
            ->selectRaw('category, COUNT(*) as count')
            ->whereNotNull('category')
            ->groupBy('category')
            ->get();

        return response()->json([
            'status' => 'success',
            'data'   => [
                'by_log_type' => $byType,
                'by_category' => $byCategory,
            ],
        ], 200);
    }

    /**
     * Housekeeping purge for logs older than given date or N days.
     * DELETE /api/activity-logs/purge?before=YYYY-MM-DD
     */
    public function purge(Request $request)
    {
        $beforeDate = $request->input('before');

        if (!$beforeDate) {
            return response()->json([
                'status'  => 'error',
                'message' => 'The "before" parameter (YYYY-MM-DD) is required.',
            ], 400);
        }

        try {
            $targetDate = Carbon::parse($beforeDate)->startOfDay();
        } catch (Throwable $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Invalid date format for "before" parameter. Use YYYY-MM-DD.',
            ], 400);
        }

        $deletedCount = ActivityLog::where('event_at', '<', $targetDate)->delete();

        return response()->json([
            'status'        => 'success',
            'message'       => 'Logs purged successfully',
            'deleted_count' => $deletedCount,
        ], 200);
    }
}
