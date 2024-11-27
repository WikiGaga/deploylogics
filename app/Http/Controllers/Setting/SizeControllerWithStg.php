<?php

namespace App\Http\Controllers\Setting;

use App\Http\Controllers\Controller;
use App\Library\Utilities;
use App\Models\TblDefiSize;
use App\Models\TblPurcBarcodeSize;
use App\Models\TblStgActions;
use App\Models\TblStgFlows;
use App\Models\TblStgFormCases;
use App\Models\TblStgFormFlowProcess;
use App\Models\TblStgFormLog;
use Illuminate\Http\Request;


// db and Validator
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;
use Validator;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class SizeControllerWithStg extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public static $page_title = 'Size';
    public static $redirect_url = 'size';
    public static $menu_dtl_id = '10';

    public function getFormFlows($menu_dtl_id,$flow_id,$form_id = null){
        $StgFormCases = TblStgFormCases::with('form_flows')->where('menu_dtl_id',$menu_dtl_id);
        if(isset($form_id)){
            $StgFormCases = $StgFormCases->where('form_id',$form_id);
        }
        $StgFormCases = $StgFormCases->first();
        $flows['all'] = [];
        $flows['current'] = [];
        $flows['next'] = [];
        $stg = true;
        $nextKey = 0;
        foreach ($StgFormCases->form_flows as $form_flows){
            $stg_flows = TblStgFlows::where('stg_flows_id',$form_flows->stg_flows_id)->first();
            if(!empty($flow_id) && $flow_id == $stg_flows->stg_flows_id){
                $flows['current'] = $stg_flows;
            }
            if($form_id == null &&  $stg == true){
                $flows['current'] = $stg_flows;
                $stg = false;
            }
            array_push($flows['all'], $stg_flows);
        }
        foreach ($flows['all'] as $key=>$all){
            if( $flows['current']->stg_flows_id == $all->stg_flows_id){
                $nextKey = $key+1;
                $prevKey = $key-1;
            }
        }
        $flows['next'] = isset($flows['all'][$nextKey])?$flows['all'][$nextKey]:null;
        $flows['prev'] = isset($flows['all'][$prevKey])?$flows['all'][$prevKey]:null;
        return $flows;
    }

    public function getFormAction($menu_dtl_id,$flow_id,$form_id = null){
        $actions = [];
        if(empty($flow_id)){
            return $actions;
        }
        $StgFormCases = TblStgFormCases::with('form_flows')->where('menu_dtl_id',$menu_dtl_id);
        if(isset($form_id)){
            $StgFormCases = $StgFormCases->where('form_id',$form_id);
        }
        $StgFormCases = $StgFormCases->first();

        $FormFlowProcess = TblStgFormFlowProcess::where('stg_form_cases_id',$StgFormCases->stg_form_cases_id)
            ->where('stg_flows_id',$flow_id)->where('process_type','=','App\Models\TblStgActions')->get();
        $actions = [];
        foreach ($FormFlowProcess as $action){
            $act = TblStgActions::where('stg_actions_id',$action->process_id)
                ->where('stg_actions_entry_status',1)->first();
            array_push($actions, $act);
        }
        return $actions;
    }

    public function getFormActivity($menu_dtl_id,$form_id){
        $activity = TblStgFormLog::with('action_btn_dtl','flow_dtl')->where('menu_dtl_id',$menu_dtl_id)->where('form_id',$form_id)->orderBy('created_at','desc')->get();
        return $activity;
    }

    public function getFormUserAccess($menu_dtl_id,$current_stg_id,$form_id = null){

        $StgFormCases = TblStgFormCases::with('form_flows')->where('menu_dtl_id',$menu_dtl_id);
        if(isset($form_id)){
            $StgFormCases = $StgFormCases->where('form_id',$form_id);
        }
        $StgFormCases = $StgFormCases->first();

        $FormFlowProcess = TblStgFormFlowProcess::where('stg_form_cases_id',$StgFormCases->stg_form_cases_id)
            ->where('stg_flows_id',$current_stg_id)->where('process_type','=','App\Models\User')->get();

        $user_access = false;
        foreach ($FormFlowProcess as $user){
            if($user->process_id == auth()->user()->id){
                $user_access = true;
            }
        }
        return $user_access;
    }
    public function getCase($menu_dtl_id,$form_id = null){
        $StgFormCases = TblStgFormCases::with('form_flows')->where('menu_dtl_id',$menu_dtl_id);
        if(isset($form_id)){
            $StgFormCases = $StgFormCases->where('form_id',$form_id);
        }
        $StgFormCases = $StgFormCases->first();
        return $StgFormCases;
    }
    /*
    Flows
        => all
        => current
        => next
        => prev
    Buttons
        => all

    userAccess = true or false

 * */
    public function create($id = null)
    {
        $data['page_data'] = [];
        $data['page_data']['title'] = self::$page_title;
        $data['page_data']['path_index'] = $this->prefixIndexPage.self::$redirect_url;
        if(isset($id)){
            if(TblDefiSize::where('size_id','LIKE',$id)->exists()){
                $data['page_data'] = array_merge($data['page_data'], Utilities::editForm());
                $data['permission'] = self::$menu_dtl_id.'-edit';
                $data['id'] = $id;
                $data['current'] = TblDefiSize::where('size_id',$id)->first();
                // stg code activity
                $form_id = $data['current']->size_id;
                $data['stg']['activity'] = $this->getFormActivity(self::$menu_dtl_id,$form_id);
                if($data['current']->staging_apply != 1){
                    $data['stg']['user_access'] = $this->getFormUserAccess(self::$menu_dtl_id,$data['current']->current_stg_id);
                }
                if($data['current']->staging_apply == 1){
                    $data['stg']['user_access'] = true;
                }
               // dd($data['stg']['activity']->toArray());
            }else{
                abort('404');
            }
        }else{
            $data['permission'] = self::$menu_dtl_id.'-create';
            $data['page_data'] = array_merge($data['page_data'], Utilities::newForm());
        }
        // start stg code flows and btns
        $data['stg']['current_stg_id'] = isset($data['current']->current_stg_id)?$data['current']->current_stg_id:'';
        $data['stg']['staging_apply'] = isset($data['current']->staging_apply)?$data['current']->staging_apply:0;
        $data['stg']['flows'] = $this->getFormFlows(self::$menu_dtl_id,$data['stg']['current_stg_id']);
        $data['stg']['btns'] = $this->getFormAction(self::$menu_dtl_id,$data['stg']['current_stg_id']);
        $data['stg']['current_case'] = $this->getCase(self::$menu_dtl_id);
        // end stg code flows and btns
       // dd($data['stg']['current_case']);
        $data['size'] = TblDefiSize::where('size_entry_status',1)->get();
        return view('setting.size.form',compact('data'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $id = null)
    {
       // dd($request->toArray());
        $data = [];
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:100'
        ]);
        if ($validator->fails()) {
            $data['validator_errors'] = $validator->errors();
            return $this->jsonErrorResponse($data, trans('message.required_fields'), 422);
        }
        DB::beginTransaction();
        try{
            if(isset($id)){
                $size = TblDefiSize::where('size_id',$id)->first();
            }else{
                $size = new TblDefiSize();
                $size->size_id = Utilities::uuid();
            }
            $size->size_name = $request->name;
            $size->size_entry_status = isset($request->size_entry_status)?"1":"0";
            $size->business_id = auth()->user()->business_id;
            $size->company_id = auth()->user()->company_id;
            $size->branch_id = auth()->user()->branch_id;
            $size->size_user_id = auth()->user()->id;
            $size->current_stg_id = $request->next_flow_id;
            $size->staging_apply = ($request->next_flow_id == null)?1:0;
            $size->save();

            // start for staging log code
            $form_id = $size->size_id;
            $current_flow_id = $request->current_flow_id;
            $current_actions_id = $request->action;
            $this->StgFormLog(self::$menu_dtl_id,$form_id,$current_flow_id,$current_actions_id);
            // end for staging log code
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
            $data['redirect'] = $this->prefixIndexPage.self::$redirect_url;;
            return $this->jsonSuccessResponse($data, trans('message.update'), 200);
        }else{
            $data = array_merge($data, Utilities::returnJsonNewForm());
            return $this->jsonSuccessResponse($data, trans('message.create'), 200);
        }
    }

    public function StgFormLog($menu_dtl_id,$form_id,$current_flow_id,$current_actions_id){
        $StgFormCases = TblStgFormCases::with('form_flows')->where('menu_dtl_id',$menu_dtl_id);
        if(isset($form_id)){
           // $StgFormCases = $StgFormCases->where('form_id',$form_id);
        }
        $StgFormCases = $StgFormCases->first();
        $TblStgFormLog = TblStgFormLog::create([
            'stg_form_log_id' => Utilities::uuid(),
            'menu_dtl_id' => $menu_dtl_id,
            'form_id' => $form_id,
            'stg_form_cases_id' => $StgFormCases->stg_form_cases_id,
            'user_id' => auth()->user()->id,
            'stg_flows_id' => $current_flow_id,
            'stg_actions_id' => $current_actions_id,
            'stg_form_log_entry_status' => 1,
            'stg_form_log_user_id' => auth()->user()->id,
            'business_id' => auth()->user()->business_id,
            'company_id' => auth()->user()->company_id,
            'branch_id' => auth()->user()->branch_id,
        ]);
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
        if(TblDefiSize::where('size_id',$id)->exists()){
            $data['current'] = TblDefiSize::where('size_id',$id)->first();
            return view('setting.size.edit', compact('data'));
        }else{
            abort('404');
        }
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
        try{
            $size = TblDefiSize::where('size_id',$id)->first();

            $size->size_name = $request->name;
            $size->size_entry_status = isset($request->size_entry_status)?"1":"0";
            $size->business_id = auth()->user()->business_id;
            $size->company_id = auth()->user()->company_id;
            $size->branch_id = auth()->user()->branch_id;
            $size->size_user_id = auth()->user()->id;
            $size->save();

            return $this->returnJsonSucccess('Size successfully updated.',200);

        }catch(\Exception $e){

            return $this->returnJsonError($e->getMessage(),201);

        }
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

            $SizeTag = TblPurcBarcodeSize::where('size_id',$id)->first();

            if($SizeTag == null)
            {
                $size = TblDefiSize::where('size_id',$id)->first();
                $size->delete();
            }else{
                return $this->jsonErrorResponse($data, trans('message.not_delete'), 200);
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
        return $this->jsonSuccessResponse($data, trans('message.delete'), 200);
    }
}
