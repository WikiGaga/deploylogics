<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\PosOrderVoucherService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Exception;

class PosOrderVoucherController extends Controller
{
    public function store(Request $request, PosOrderVoucherService $service)
    {
        $validator = Validator::make($request->all(), [
            'order_id' => ['required_without:order_ids'],
            'order_ids' => ['required_without:order_id', 'array', 'min:1'],
            'order_ids.*' => ['required'],
            'action' => ['nullable', 'in:sync,delete'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $action = $request->input('action', 'sync');
        $orderIds = $request->filled('order_ids')
            ? array_values(array_unique($request->input('order_ids')))
            : [$request->input('order_id')];

        $results = [];

        try {
            DB::beginTransaction();

            foreach ($orderIds as $orderId) {
                if ($action === 'delete') {
                    $deleted = $service->deleteOrderVouchers($orderId);
                    $results[] = [
                        'order_id' => $orderId,
                        'action' => 'deleted',
                        'deleted_rows' => $deleted,
                    ];
                    continue;
                }

                $results[] = $service->syncOrder($orderId);
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'data' => ['results' => $results],
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Order voucher processing completed',
            'data' => ['results' => $results],
        ]);
    }
}
