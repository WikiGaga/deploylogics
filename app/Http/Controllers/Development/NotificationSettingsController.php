<?php

namespace App\Http\Controllers\Development;

use App\Http\Controllers\Controller;
use App\Library\Utilities;
use App\Models\TblNotificationSetting;
use App\Models\TblSoftListingStudio;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class NotificationSettingsController extends Controller
{
    public function index(Request $request, $id = null)
    {
        $data = [];

        
        $data['notification_settings'] = DB::table('notification_settings')->get();
        $data['listings'] = TblSoftListingStudio::select('listing_studio_title', 'listing_studio_table_name')->get();

        if(isset($id)){
            $data['notification'] = TblNotificationSetting::where('id', $id)->first();
            if(!$data['notification']){
                return response()->json([
                    'success' => false,
                    'message' => 'Notification not found'
                ], 404);
            }
        }

        // dd($data);
        return view('development.notification_settings.form', compact('data'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'notification_title' => 'required|string',
            'notification_form' => 'required|string',
            'push_notification_status' => 'nullable',
            'mail_status' => 'nullable',
            'sms_status' => 'nullable',
            'whatsapp_status' => 'nullable',
            'whatsapp_template' => 'nullable|string|required_if:whatsapp_status,on',
            'notification_message' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->jsonErrorResponse('Validation Error', $validator->errors(), 422);
        }

        DB::beginTransaction();

        try {
            $notification = new TblNotificationSetting();
            // $notification->id = Utilities::uuid();
            $notification->title = $request->notification_title;
            $notification->message = $request->notification_message;
            $notification->key = $request->notification_form;
            $notification->push_notification_status = $request->push_notification_status == 'on' ? 'active' : 'inactive';
            $notification->mail_status = $request->mail_status == 'on' ? 'active' : 'inactive';
            $notification->sms_status = $request->sms_status == 'on' ? 'active' : 'inactive';
            $notification->whatsapp_status = $request->whatsapp_status == 'on' ? 'active' : 'inactive';
            $notification->whatsapp_template = $request->whatsapp_template;
            $notification->save();

            DB::commit();

            return $this->jsonSuccessResponse('Notification Settings created successfully!', $notification);

        } catch (Exception $e) {
            DB::rollback();

            return $this->jsonErrorResponse('Error creating Notification Settings', ['message' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        dd($request->all(), $id);
        $validator = Validator::make($request->all(), [
            'notification_title' => 'required|string',
            'notification_form' => 'required|string',
            'push_notification_status' => 'nullable',
            'mail_status' => 'nullable',
            'sms_status' => 'nullable',
            'whatsapp_status' => 'nullable',
            'whatsapp_template' => 'nullable|string|required_if:whatsapp_status,on',
            'notification_message' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->jsonErrorResponse('Validation Error', $validator->errors(), 422);
        }

        DB::beginTransaction();

        try {
            $notification = TblNotificationSetting::findOrFail($id);
            $notification->title = $request->notification_title;
            $notification->message = $request->notification_message;
            $notification->key = $request->notification_form;
            $notification->push_notification_status = $request->push_notification_status;
            $notification->mail_status = $request->mail_status;
            $notification->sms_status = $request->sms_status;
            $notification->whatsapp_status = $request->whatsapp_status;
            $notification->whatsapp_template = $request->whatsapp_template;
            $notification->save();

            DB::commit();

            return $this->jsonSuccessResponse('Notification Settings updated successfully!', $notification);

        } catch (Exception $e) {
            DB::rollback();

            return $this->jsonErrorResponse('Error updating Notification Settings', ['message' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $notification = TblNotificationSetting::findOrFail($id);
            $notification->delete();

            return $this->jsonSuccessResponse('Notification deleted successfully!', null);

        } catch (Exception $e) {
            return $this->jsonErrorResponse('Error deleting Notification', ['message' => $e->getMessage()], 500);
        }
    }
}
