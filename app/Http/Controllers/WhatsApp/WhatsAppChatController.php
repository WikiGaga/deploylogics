<?php

namespace App\Http\Controllers\WhatsApp;

use Exception,Validator;
use App\Library\Utilities;
use Illuminate\Http\Request;
use App\Models\TblWhatsAppChat;
use App\Models\TblWhatsAppGroup;
use App\Models\TblWhatsAppContact;
use App\Models\TblWhatsAppMessage;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Database\QueryException;
use App\Models\TblWhatsAppGroupContacts;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Http\Controllers\Api\WhatsApp\WhatsAppApiController;
use App\Models\TblSoftBranch;
use App\Models\TblWhatsAppLocations;
use Illuminate\Auth\Events\Validated;

class WhatsAppChatController extends Controller
{
    public function index(Request $request){
        $data = [];

        $data['contacts'] = TblWhatsAppContact::select('phone_no','cnt_name')
        ->withCount('unreadMessages')
        ->orderBy('last_message', 'asc')
        ->orderBy('cnt_is_group' , 'desc')
        ->get();
        $data['groups'] = TblWhatsAppGroup::where('is_active' , 1)->get();

        $data['locations']  = TblWhatsAppLocations::get();
        
        return view('whatsapp.customer_support.chat' , compact('data'));
    }

    public function getChatWindow(Request $request , $phoneNo = null){
        $data = [];

        if(!isset($phoneNo)){
            return $this->jsonErrorResponse($data , 'Something went wrong!' , 403);
        }
        
        if(!TblWhatsAppContact::where('phone_no' , $phoneNo)->exists()){
            return $this->jsonErrorResponse($data , 'Contact Not Exists.' , 403);
        }

        DB::beginTransaction();
        try {
            $userChat = TblWhatsAppChat::with('contact','user')
            ->where('phone_no' , $phoneNo)
            ->limit(100)
            ->orderBy('receive_at','asc')
            ->get();

            $update = TblWhatsAppChat::where('phone_no', '=', $phoneNo)
            ->update(['message_status' => 'read']);

            $userInfo = TblWhatsAppContact::where('phone_no' , $phoneNo)->first();

            $branches = TblSoftBranch::where('branch_active_status' , 1)->get();

            $data['chatWindow'] = view('whatsapp.customer_support.partials.chat_window' , compact('userChat','userInfo','branches'))->render();
        } catch (Exception $e) {
            DB::rollBack();
            return $this->jsonErrorResponse($data , $e->getMessage() , 404);
        }
        DB::commit();
        return $this->jsonSuccessResponse($data , 'Chat Window Loaded' , 200);
    }

    public function sendCustomerMessage(Request $request , $phoneNo = null){
        $data = [];
        if(!isset($phoneNo)){
            return $this->jsonErrorResponse($data , 'Invalid Message Request' , 500);
        }

        $validator = Validator::make($request->all() , [
            'phoneNo'       =>  'required|numeric',
            'messageType'   =>  'required',
            'message'       =>  'required',
            'sender'        =>  'required',
            'messageSentTo' =>  'required'
        ]);

        if($validator->fails()){
            return $this->jsonErrorResponse($data , 'Invalid Request' , 500);
        }

        $data['messageId'] = $request->messageId;
        $messageSentTo = trim($request->messageSentTo);

        DB::beginTransaction();
        try {
            if($messageSentTo == 'single'){
                $this->sendAndSaveMessage($phoneNo , $request->message , $request->messageType , $request->sender);
            }else{
                $groupId = $phoneNo;
                $contacts = TblWhatsAppGroupContacts::where('grp_id' , $phoneNo)->select('phone_no')->get();
                foreach ($contacts as $contact) {
                    $this->sendAndSaveMessage($contact->phone_no , $request->message , $request->messageType , $request->sender , $groupId);
                }
                // Save Group Sent Message For Once Only.
                $this->saveWhatsAppMessage([], 'group-message' , $groupId , $request->messageType , $request->message, 1, 0, 'read', $request->sender);
            }
              
        } catch (Exception $e) {
            DB::rollBack();
            return $this->jsonErrorResponse($data , $e->getMessage() , 500);
        }
        DB::commit();
        return $this->jsonSuccessResponse($data , 'Message Sent!' , 200);
    }

    public function sendAndSaveMessage($phoneNo , $message , $messageType , $sender , $groupId = ''){
        $data = [];

        $components = [
            [
                "type" => "body",
                "parameters" => [
                    [
                        "type"=> "text",
                        "text" => $message
                    ]
                ]
            ]
        ];

        $lastMessage = TblWhatsAppContact::where('phone_no' , $phoneNo)->select('last_message')->first();
       
        if(isset($lastMessage->last_message) && strtotime($lastMessage->last_message) <= strtotime('1 day')){
            $response = WhatsAppApiController::sendWhatsAppText($message , $phoneNo);
        }else{
            // Send Template Message (Bussiness Initiated)
            $response = WhatsAppApiController::sendWhatsAppTemplate('send_support_message' , $phoneNo , 'ar' , $components);
            dump($response);
        }

        $response = json_decode($response);
        if(isset($response->error)){
            return $this->jsonErrorResponse($data , $response->error->message , 500);
        }else{
            $messageId = $response->messages[0]->id;
            $this->saveWhatsAppMessage($response, $messageId , $phoneNo , $messageType , $message, 1, 0, 'read', $sender, false);
            
        }
    }

    public function getCustomerGroups(Request $request , $phoneNo = null){
        $data = [];
        if(!isset($phoneNo)){
            return $this->jsonErrorResponse($data , "Something went wrong" , 403);
        }

        $groups = TblWhatsAppGroup::where('is_active' , 1)->get();

        $data['html'] = view('whatsapp.customer_support.partials.groupspopup' , compact('groups','phoneNo'))->render();

        return $this->jsonSuccessResponse($data , 'Load Popup' , 200);
    }

    public function getGroupContacts(Request $request , $groupId = null){
        $data = [];

        if(!isset($groupId)){
            return $this->jsonErrorResponse($data , 'Something went wrong' , 403);
        }

        $data['contacts'] = TblWhatsAppContact::with(['groups' => function($q) use ($groupId){
            $q->where('grp_id' , $groupId);
        }])->where('is_active' , 1)
        ->where('cnt_is_group' , 0)
        ->get();

        return $this->jsonSuccessResponse($data , "Contacts Loaded" , 200);
    }

    public function getContactsofGroup(Request $request , $groupId = null){

        $data = [];
        if(!isset($groupId)){
            return $this->jsonErrorResponse($data , 'Something went wrong' , 403);
        }

        $data['contacts'] = TblWhatsAppGroupContacts::with('contact')->where('grp_id' , $groupId)->get();

        return $this->jsonSuccessResponse($data , "Group Contacts Loaded" , 200);

    }

    public function markMessageRead(Request $request , $phoneNo = null){
        if(isset($phoneNo)){
            TblWhatsAppMessage::where('phone_no', '=', $phoneNo)
            ->update(['message_status' => 'read']);

            return $this->jsonSuccessResponse([] , 'Messages Status Updated' , 200);
        }
        return $this->jsonErrorResponse([] , 'Phone No. not set.' , 403);
    }

    public function addContactInGroup(Request $request){
        $data = [];

        dd($request->toArray());
        $validator = Validator::make($request->all() , [
            'selectedGroup' => 'required|numeric',
            'selectedContacts' => 'required|array'
        ]);

        if($validator->fails()){
            return $this->jsonErrorResponse($data , 'Please Select Contacts & Group.' , 403);
        }

        // Delete Already Inserted Contacts
        DB::beginTransaction();
        try {
            TblWhatsAppGroupContacts::where('grp_id' , $request->selectedGroup)->delete();
            
            if(isset($request->selectedContacts) && count($request->selectedContacts) > 0){
                foreach ($request->selectedContacts as $contact) {
                    $contactGrp = new TblWhatsAppGroupContacts();
                    $contactGrp->group_contact_id = Utilities::uuid();
                    $contactGrp->grp_id = $request->selectedGroup;
                    $contactGrp->phone_no = $contact;
                    $contactGrp->save();
                }    
            }
        }catch (QueryException $e) {
            DB::rollback();
            return $this->jsonErrorResponse($data, $e->getMessage(), 200);
        } catch (ModelNotFoundException $e) {
            DB::rollback();
            return $this->jsonErrorResponse($data, $e->getMessage(), 200);
        } catch (ValidationException $e) {
            DB::rollback();
            return $this->jsonErrorResponse($data, $e->getMessage(), 200);
        } catch (Exception $e) {
            DB::rollback();
            return $this->jsonErrorResponse($data, $e->getMessage(), 200);
        }
        DB::commit();

        return $this->jsonSuccessResponse($data , "Contacts Added In Group" , 200);
    }

    public function storeLocation(Request $request , $id = null){

        $data = [];
        $validator = Validator::make($request->all() , [
            'waLocationLat'     =>  'required|min:-90|max:90|numeric',
            'waLocationLng'     =>  'required|min:-90|max:90|numeric',
            'waLocationName'    =>  'required',
            'waLocationAddress' =>  'required'
        ]);

        if($validator->fails()){
            return $this->jsonErrorResponse($data , trans('message.required_fields') , 422);
        }

        DB::beginTransaction();
        try {
            if(isset($id)){
                $location = TblWhatsAppLocations::where('wa_location_id' , $id)->first();
            }else{
                $location = new TblWhatsAppLocations();
                $location->wa_location_id = Utilities::uuid();
            }
            $location->location_lat = $request->waLocationLat;
            $location->location_lng = $request->waLocationLng;
            $location->location_name = $request->waLocationName;
            $location->location_address = $request->waLocationAddress;
            $location->save();

            $data['location'] = $location;
        } catch (Exception $e) {
            DB::rollBack();
            return $this->jsonErrorResponse([] , $e->getMessage() , 500);
        }
        DB::commit();
        return $this->jsonSuccessResponse($data , 'Location Added!' , 200);
    }

    public function sendLocationMessage(Request $request){
        $data = [];
        $validator = Validator::make($request->all(), [
            'phoneNo'   => 'required',
            'lat'       => 'required',
            'lng'       => 'required',
            'name'      => 'required',
            'address'   => 'required',
            'sendto'    => 'required',
        ]);

        if($validator->fails()){
            return $this->jsonErrorResponse([] , "Something went wrong!" , 403);
        }   

        $phoneNo = $request->phoneNo;
        DB::beginTransaction();
        try {
            if($request->sendto == 'single'){
                $response = WhatsAppApiController::sendWhatsAppLocation($phoneNo , $request->lat , $request->lng, $request->name , $request->address);
                dump($response);
                // $this->sendAndSaveLocationMessage($phoneNo , $request->message , $request->messageType , $request->sender);
            }else{
                $groupId = $phoneNo;
                $contacts = TblWhatsAppGroupContacts::where('grp_id' , $phoneNo)->select('phone_no')->get();
                foreach ($contacts as $contact) {
                    $this->sendAndSaveLocationMessage($contact->phone_no , $request->message , $request->messageType , $request->sender , $groupId);
                }
                // Save Group Sent Message For Once Only.
                $this->saveWhatsAppMessage([], 'group-message' , $groupId , $request->messageType , $request->message, 1, 0, 'read', $request->sender);
            }
              
        } catch (Exception $e) {
            DB::rollBack();
            return $this->jsonErrorResponse($data , $e->getMessage() , 500);
        }
        DB::commit();
        return $this->jsonSuccessResponse($data , 'Message Sent!' , 200);
    }
}
