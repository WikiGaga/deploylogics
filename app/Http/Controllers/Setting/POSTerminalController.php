<?php

namespace App\Http\Controllers\Setting;

use App\Http\Controllers\Controller;
use App\Library\Utilities;
use App\Models\TblSoftPOSTerminal;
use App\Models\TblAccCoa;
use App\Models\TblSoftBranch;
use Illuminate\Http\Request;

// db and Validator
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class POSTerminalController extends Controller
{
    public static $page_title = 'POS Terminal';
    public static $redirect_url = 'pos-terminal';
    public static $menu_dtl_id = '161';
    //public static $menu_dtl_id = '140';

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
    public function create($id = null)
    {
        // $chart_bank_group = TblAccCoa::where('chart_Account_id',Session::get('dataSession')->bank_group)->where(Utilities::currentBC())->first('chart_code');
        // $bank_group = substr($chart_bank_group->chart_code,0,7);

        $data['page_data'] = [];
        $data['page_data']['title'] = self::$page_title;
        $data['page_data']['path_index'] = $this->prefixIndexPage.self::$redirect_url;
        $data['page_data']['create'] = '/'.self::$redirect_url.$this->prefixCreatePage;
        if(isset($id)){
            if(TblSoftPOSTerminal::where('terminal_id','LIKE',$id)->exists()){
                $data['page_data'] = array_merge($data['page_data'], Utilities::editForm());
                $data['permission'] = self::$menu_dtl_id.'-edit';
                $data['id'] = $id;
                $data['current'] = TblSoftPOSTerminal::where('terminal_id',$id)->first();
            }else{
                abort('404');
            }
        }else{
            $data['permission'] = self::$menu_dtl_id.'-create';
            $data['page_data'] = array_merge($data['page_data'], Utilities::newForm());
        }
        $data['branches'] = TblSoftBranch::all();
        $data['acc_code']= TblAccCoa::select('chart_code','chart_name','chart_Account_id')->where('chart_level', '=',4)->where('chart_code','like', '6-01-02'."%")->where(Utilities::currentBC())->orderBy('chart_code')->get();
        return view('setting.pos_terminal.form',compact('data'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $id = null)
    {
        $data = [];
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:20',
            'mac_address' => 'required|max:20'
        ]);
        if ($validator->fails()) {
            $data['validator_errors'] = $validator->errors();
            return $this->jsonErrorResponse($data, trans('message.required_fields'), 422);
        }
        DB::beginTransaction();
        try{

            if(isset($id)){
                $terminal = TblSoftPOSTerminal::where('terminal_id',$id)->first();
                $terminal->update_id = Utilities::uuid();
            }else{
                $terminal = new TblSoftPOSTerminal();
                $terminal->terminal_id = Utilities::uuid();
            }
            $form_id = $terminal->terminal_id;
            $terminal->terminal_name = $request->name;
            $terminal->terminal_mac_address = $request->mac_address;
            $terminal->branch_id = $request->branch_id;
            $terminal->chart_id = $request->chart_id;
            $terminal->fbr_pos_id = $request->fbr_pos_id;
            $terminal->pos_register_no = $request->pos_register_no;
            $terminal->pos_ntn_no = $request->pos_ntn_no;
            $terminal->pos_stn_no = $request->pos_stn_no;
            $terminal->business_id = auth()->user()->business_id;
            $terminal->company_id = auth()->user()->company_id;
            $terminal->user_id = auth()->user()->id;
            $terminal->save();

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

            $terminal = TblSoftPOSTerminal::where('terminal_id',$id)->first();
            $terminal->delete();

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
