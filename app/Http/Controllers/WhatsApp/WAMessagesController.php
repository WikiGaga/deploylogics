<?php

namespace App\Http\Controllers\WhatsApp;

use Exception;
use Validator;
use Storage;
use App\Library\Utilities;
use Illuminate\Http\Request;
use App\Models\TblWhatsAppApiCmd;
use App\Models\TblWhatsAppMessage;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Database\QueryException;
use App\Models\TblWhatsAppApiCmdParameter;
use App\Models\TblWhatsAppMessageDtl;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class WAMessagesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public static $page_title = 'WhatsApp Messages';
    public static $redirect_url = 'wa-messages';
    public static $menu_dtl_id = '224';

    public function create($id = null){

        $data['page_data'] = [];
        $data['page_data']['title'] = self::$page_title;
        $data['page_data']['path_index'] = $this->prefixIndexPage.self::$redirect_url;
        $data['page_data']['create'] = '/'.self::$redirect_url.$this->prefixCreatePage;
        if(isset($id)){
            if(TblWhatsAppMessage::where('msg_id','LIKE',$id)->exists()){
                $data['page_data'] = array_merge($data['page_data'], Utilities::editForm());
                $data['permission'] = self::$menu_dtl_id.'-edit';
                $data['id'] = $id;
                $data['current'] = TblWhatsAppMessage::with('dtls')->where('msg_id',$id)->first();
                $data['parameters'] = TblWhatsAppApiCmdParameter::where('cmd_id' , $data['current']->cmd_id)->orderBy('field_type' , 'desc')->get();
                
                // dd($data);
            }else{
                abort('404');
            }
        }else{
            $data['permission'] = self::$menu_dtl_id.'-create';
            $data['page_data'] = array_merge($data['page_data'], Utilities::newForm());
        }

        
        $data['types'] = TblWhatsAppApiCmd::get();

        return view('whatsapp.message.form',compact('data'));
    }

    public function store(Request $request, $id = null){
        $data = [];
        $validator = Validator::make($request->all(), [
            'cmd_type' => 'required',
            'remarks' => 'required'
        ]);
        if ($validator->fails()) {
            $data['validator_errors'] = $validator->errors();
            return $this->jsonErrorResponse($data, trans('message.required_fields'), 422);
        }

        DB::beginTransaction();
        try{
            if(isset($id)){
                $msg = TblWhatsAppMessage::where('msg_id',$id)->first();
            }else{
                $msg = new TblWhatsAppMessage();
                $msg->msg_id = Utilities::uuid();
            }

            $form_id = $msg->msg_id;
            
            $msg->cmd_id = $request->cmd_type;
            $msg->remarks = $request->remarks;
            $msg->msg_status = isset($request->is_active)?"Y":"N";
            $msg->business_id = auth()->user()->business_id;
            $msg->company_id = auth()->user()->company_id;
            $msg->branch_id = auth()->user()->branch_id;
            $msg->user_id = auth()->user()->id;
            $msg->save();

            if(isset($id)){
                TblWhatsAppMessageDtl::where('msg_id' , $id)->delete();
            }

            if(isset($request->pd)){
                foreach($request->pd as $par_id => $value) {
                    
                    if(is_array($value)){
                        $value = implode(',' , $value);
                    }

                    $dtl = new TblWhatsAppMessageDtl();
                    $dtl->msg_id = $msg->msg_id;
                    $dtl->msg_id_dtl = Utilities::uuid();
                    $dtl->cmd_id = $request->cmd_type;
                    $dtl->par_id = $par_id;
                    $dtl->par_value = $value;
                    $dtl->save();
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
        if(isset($id)){
            $data = array_merge($data, Utilities::returnJsonEditForm());
            $data['redirect'] = $this->prefixIndexPage.self::$redirect_url;
            return $this->jsonSuccessResponse($data, trans('message.update'), 200);
        }else{
            $data = array_merge($data, Utilities::returnJsonNewForm());
            $data['redirect'] = '/'.self::$redirect_url.$this->prefixCreatePage.'/'.$form_id;
            return $this->jsonSuccessResponse($data, trans('message.create'), 200);
        }
    }

    public function parameters(Request $request , $id = null){
        $data = [];
        if(isset($id)){
            // Get the Parameters.
            $data['parameters'] = TblWhatsAppApiCmdParameter::where('cmd_id' , $id)->orderBy('field_type' , 'desc')->get();
            return $this->jsonSuccessResponse($data, 'Success!', 200);
        }
        return $this->jsonErrorResponse($data, 'Something Went Wrong!', 200);
    }

    public function uploadAttachment(Request $request){
        $validator = Validator::make($request->all() , [
            'file' => 'required|max:1000',
        ]);

        if($validator->fails()){
            $data['validator_errors'] = $validator->errors();
            return $this->jsonErrorResponse($data, trans('message.required_fields'), 422);
        }else{
            $image = $request->file('file')[0];
            $imageName = $image->getClientOriginalName();
            $imageName = pathinfo($imageName , PATHINFO_FILENAME);
            $imageName = $this->clean($imageName);
            $filename =  $imageName . rand(0,99) . '.' . $image->getClientOriginalExtension();
            
            // if (!file_exists(public_path('/uploads/attachments/'))) {
            //     mkdir(public_path('/uploads/attachments/'), 0777, true);
            // }
            
            $upload_success = Storage::disk('public')->putFileAs(
                '/uploads/attachments/',
                $image,
                $filename
            );
            // $upload_success = Image::make($image->getRealPath())->save($path);
            if( $upload_success ) {
                return url('/storage/uploads/attachments' , [$filename]);
            } else {
                return 'failed';
            }
        }
    }

    public function destroy($id){
        $data = [];
        DB::beginTransaction();
        try{
            $msg =TblWhatsAppMessage::where('msg_id',$id)->first();
            $msg->dtls()->delete();
            $msg->delete();
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
        return $this->jsonSuccessResponse($data, trans('message.delete'), 200);
    }
}
