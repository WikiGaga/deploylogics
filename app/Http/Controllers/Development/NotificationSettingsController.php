<?php

namespace App\Http\Controllers\Development;

use App\Http\Controllers\Controller;
use App\Models\TblNotificationSetting;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class NotificationSettingsController extends Controller
{
    public function index()
    {
        $data = [];

        
        $data['notification_settings'] = DB::table('notification_settings')->get();

        // dd($data['notification_settings']);

        return view('development.notification_settings.form', compact('data'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'menu_flow_criteria_name' => 'required|string',
            'menu_flow_criteria_apply_at' => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();

        try {
            $notification = new TblNotificationSetting();
            $notification->menu_flow_criteria_id = $this->generateUuid();
            $notification->menu_flow_criteria_dtl_id = $this->generateReferenceId();
            $notification->menu_flow_criteria_name = $request->menu_flow_criteria_name;
            $notification->menu_flow_criteria_apply_at = date('Y-m-d', strtotime($request->menu_flow_criteria_apply_at));
            $notification->menu_flow_criteria_status = 1;
            $notification->menu_flow_criteria_entry_status = 1;
            $notification->business_id = auth()->user()->business_id;
            $notification->company_id = auth()->user()->company_id;
            $notification->branch_id = auth()->user()->branch_id;
            $notification->created_by = auth()->user()->id;
            $notification->save();

            if ($request->has('criteria_conditions')) {
                $this->storeCriteriaConditions($notification->menu_flow_criteria_id, $request->criteria_conditions);
            }

            if ($request->has('flow_criteria_data')) {
                $this->storeFlowStages($notification->menu_flow_criteria_id, $request->flow_criteria_data);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Notification Settings saved successfully!',
                'data' => $notification
            ], 200);

        } catch (Exception $e) {
            DB::rollback();

            return response()->json([
                'success' => false,
                'message' => 'Error saving Notification Settings',
                'error' => $e->getMessage(),
                'line' => $e->getLine()
            ], 500);
        }
    }

    public function updateNotificationStatus(Request $request, $id)
    {
        try {
            $notification = TblNotificationSetting::findOrFail($id);
            $notification->menu_flow_criteria_status = $request->status;
            $notification->save();

            return response()->json([
                'success' => true,
                'message' => 'Notification status updated successfully!',
                'data' => $notification
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating notification status',
                'error' => $e->getMessage(),
                'line' => $e->getLine()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $notification = TblNotificationSetting::findOrFail($id);
            $notification->delete();

            return response()->json([
                'success' => true,
                'message' => 'Notification deleted successfully!'
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting notification',
                'error' => $e->getMessage(),
                'line' => $e->getLine()
            ], 500);
        }
    }
}
