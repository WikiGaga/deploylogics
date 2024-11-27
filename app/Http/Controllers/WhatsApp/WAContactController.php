<?php

namespace App\Http\Controllers\WhatsApp;

use Exception;
use Validator;
use App\Library\Utilities;
use Illuminate\Http\Request;
use App\Models\TblDefiCountry;
use App\Models\TblWhatsAppGroup;
use App\Models\TblWhatsAppContact;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class WAContactController extends Controller
{

    public static $page_title = 'WhatsApp Contact';
    public static $redirect_url = 'wa-contact';
    public static $menu_dtl_id = '222';

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($id = null)
    {
        $data['page_data'] = [];
        $data['page_data']['title'] = self::$page_title;
        $data['page_data']['path_index'] = $this->prefixIndexPage.self::$redirect_url;
        $data['page_data']['create'] = '/'.self::$redirect_url.$this->prefixCreatePage;
        if(isset($id)){
            if(TblWhatsAppContact::where('cnt_id','LIKE',$id)->exists()){
                $data['page_data'] = array_merge($data['page_data'], Utilities::editForm());
                $data['permission'] = self::$menu_dtl_id.'-edit';
                $data['id'] = $id;
                $data['current'] = TblWhatsAppContact::with('group','city_country')->where('cnt_id',$id)->first();
            }else{
                abort('404');
            }
        }else{
            $data['permission'] = self::$menu_dtl_id.'-create';
            $data['page_data'] = array_merge($data['page_data'], Utilities::newForm());
        }
        $data['country'] = TblDefiCountry::where('country_entry_status',1)->get();
        $data['groups'] = TblWhatsAppGroup::where('is_active',1)->get();
        return view('whatsapp.contact.form',compact('data'));
    }

    public function store(Request $request , $id = null){
        $data = [];
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:100',
            'phone_no' => 'required',
            'group' => 'required',
            'city_country' => 'required',
        ]);
        if ($validator->fails()) {
            $data['validator_errors'] = $validator->errors();
            return $this->jsonErrorResponse($data, trans('message.required_fields'), 422);
        }

        DB::beginTransaction();
        try{
            if(isset($id)){
                $contact = TblWhatsAppContact::where('cnt_id',$id)->first();
            }else{
                $contact = new TblWhatsAppContact();
                $contact->cnt_id = Utilities::uuid();
            }
            $form_id = $contact->cnt_id;
            $contact->cnt_name = $request->name;
            $contact->country_id = $request->city_country;
            $contact->grp_id = $request->group;
            $contact->phone_no = $request->phone_no;
            $contact->is_active = isset($request->is_active)?"1":"0";
            $contact->is_verified = isset($request->is_verified)?"1":"0";
            $contact->business_id = auth()->user()->business_id;
            $contact->company_id = auth()->user()->company_id;
            $contact->branch_id = auth()->user()->branch_id;
            $contact->save();

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

    public function destroy($id = null){
        $data = [];
        DB::beginTransaction();
        try{
            $group =TblWhatsAppContact::where('cnt_id',$id)->first();
            $group->delete();
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
