<?php

namespace App\Http\Controllers\WhatsApp;

use Exception;
use Validator;
use App\Library\Utilities;
use App\Models\TblDefiCity;
use Illuminate\Http\Request;
use App\Models\TblDefiCountry;
use App\Models\TblWhatsAppGroup;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\TblWhatsAppContact;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class WAGroupController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public static $page_title = 'WhatsApp Group';
    public static $redirect_url = 'wa-group';
    public static $menu_dtl_id = '221';

    public function index()
    {
        //
    }

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
            if(TblWhatsAppGroup::where('grp_id','LIKE',$id)->exists()){
                $data['page_data'] = array_merge($data['page_data'], Utilities::editForm());
                $data['permission'] = self::$menu_dtl_id.'-edit';
                $data['id'] = $id;
                $data['current'] = TblWhatsAppGroup::where('grp_id',$id)->first();
            }else{
                abort('404');
            }
        }else{
            $data['permission'] = self::$menu_dtl_id.'-create';
            $data['page_data'] = array_merge($data['page_data'], Utilities::newForm());
        }

        return view('whatsapp.group.form',compact('data'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request , $id = null)
    {
        $data = [];
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:100'
        ]);
        if ($validator->fails()) {
            $data['validator_errors'] = $validator->errors();
            return $this->jsonErrorResponse($data, trans('message.required_fields'), 422);
        }
        if(!isset($id)){
            if(TblWhatsAppGroup::where('grp_name','LIKE',$request->name)->exists()){
                return $this->jsonErrorResponse($data, trans('message.duplicate_record'), 422);
            }
        }
        DB::beginTransaction();
        try{
            if(isset($id)){
                $group = TblWhatsAppGroup::where('grp_id',$id)->first();
                
                $inContact = TblWhatsAppContact::where('grp_id',$id)->where('cnt_is_group' , 1)->first();
            }else{
                $group = new TblWhatsAppGroup();
                $group->grp_id = Utilities::uuid();

                $inContact = new TblWhatsAppContact();
                $inContact->cnt_id = Utilities::uuid();
            }
            $form_id = $group->grp_id;
            $group->grp_name = $request->name;
            $group->is_active = isset($request->grp_entry_status)?"1":"0";
            $group->business_id = auth()->user()->business_id;
            $group->company_id = auth()->user()->company_id;
            $group->branch_id = auth()->user()->branch_id;
            $group->save();

            // Create Group as Contact
            $inContact->grp_id = $group->grp_id;
            $inContact->cnt_name =  $request->name . ' Group';
            $inContact->is_verified =  1;
            $inContact->is_active =  isset($request->grp_entry_status)?"1":"0";
            $inContact->phone_no = $group->grp_id;
            $inContact->business_id = auth()->user()->business_id;
            $inContact->company_id = auth()->user()->company_id;
            $inContact->branch_id = auth()->user()->branch_id;
            $inContact->cnt_is_group = 1;
            $inContact->save();


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

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $data = [];
        DB::beginTransaction();
        try{
            $group =TblWhatsAppGroup::where('grp_id',$id)->first();
            $group->inContactEntry()->delete();
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
