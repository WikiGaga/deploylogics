<?php

namespace App\Http\Controllers\Accounts;

use App\Http\Controllers\Controller;
use App\Models\TblAccCoa;
use App\Models\TblAccTaxType;
use Illuminate\Http\Request;
use App\Models\TblAccoChequeBook;
use App\Models\TblAccCoaBranches;
use App\Models\TblAccBudget;
use App\Models\TblSaleSalesOrderExpense;
use App\Models\TblSaleSalesExpense;
use App\Models\TblPurcGrnExpense;
use App\Models\TblAccoVoucher;
use App\Models\TblSoftBranch;
// db and Validator
use App\Library\Utilities;
use Illuminate\Validation\Rule;
use Validator;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Session;

class CoaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public static $page_title = 'Chart of Account';
    public static $redirect_url = 'coa';
    public static $menu_dtl_id = '24';
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request, $id = null)
    {
        $data['page_data'] = [];
        $data['page_data']['title'] = self::$page_title;
        $data['page_data']['create'] = '/'.self::$redirect_url.$this->prefixCreatePage;
        if(isset($id)){
            if(TblAccCoa::where('chart_account_id','LIKE',$id)->where(Utilities::currentBC())->exists()){
                $data['page_data'] = array_merge($data['page_data'], Utilities::editForm());
                $data['permission'] = self::$menu_dtl_id.'-edit';
                $data['id'] = $id;
                $data['current'] = TblAccCoa::with('chart_branches')->where('chart_account_id',$id)->where(Utilities::currentBC())->first();
                $data['level'] = TblAccCoa::select('chart_code','chart_name')->where('chart_level', '=', $data['current']->chart_level-1)->where(Utilities::currentBC())->get();
                $data['chart_branch'] = explode(',',$data['current']->chart_branch_id);
            }else{
                abort('404');
            }
        }else{
            $data['permission'] = self::$menu_dtl_id.'-create';
            $data['page_data'] = array_merge($data['page_data'], Utilities::newForm());
        }
        $data['branch'] = TblSoftBranch::where('branch_active_status',1)->where(Utilities::currentBC())->get();
        $data['thirdLevelAccounts'] = TblAccCoa::where('chart_level' , 3)->where('chart_account_entry_status' , 1)->get();
        if($request->type == 'acc_tree'){
            $data['main_id'] = $request->main_id;
            $data['parent_id'] = $request->parent_id;
            $data['level'] = $request->level;
            $data['page_data']['create'] = "";
            // acc create via tree modal
            return view('accounts.chart_of_account_tree.coa_form',compact('data'));
        }else{
            $data['page_data']['path_index'] =  $this->prefixIndexPage.self::$redirect_url;

            return view('accounts.chart_of_account.form',compact('data'));
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $id = null)
    {
      //  dd($request->toArray());
        $data = [];
        if(isset($id)){
            $validator = Validator::make($request->all(), [
                'name' => 'required|max:100',
                'chart_debit_limit' => 'nullable|numeric',
                'chart_credit_limit' => 'nullable|numeric'
            ]);
        }else{
            $validator = Validator::make($request->all(), [
                'name' => 'required|max:100',
                'parent_account_code' => 'required',
                'chart_debit_limit' => 'nullable|numeric',
                'chart_credit_limit' => 'nullable|numeric'
            ]);
        }

        if ($validator->fails()) {
            $data['validator_errors'] = $validator->errors();
            return $this->jsonErrorResponse($data, trans('message.required_fields'), 422);
        }

        //supplier and customer not open in chart of accouunt
        $supplier_group = "";
        if(isset(Session::get('dataSession')->supplier_group) && !empty(Session::get('dataSession')->supplier_group)){
            $chart_supplier_group = TblAccCoa::where('chart_Account_id',Session::get('dataSession')->supplier_group)->where(Utilities::currentBC())->first('chart_code');
            $supplier_group = substr($chart_supplier_group->chart_code,0,4);
        }
        $customer_group = "";
        if(isset(Session::get('dataSession')->customer_group) && !empty(Session::get('dataSession')->customer_group)) {
            $chart_customer_group = TblAccCoa::where('chart_Account_id',Session::get('dataSession')->customer_group)->where(Utilities::currentBC())->first('chart_code');
            $customer_group = substr($chart_customer_group->chart_code,0,4);
        }

        if(isset($request->chart_code)){
            $code = substr($request->chart_code,0,4);
            if($code == $customer_group || $code == $supplier_group){
                if($id){
                    return $this->returnjsonerror(" Account Not Allow to Update",201);
                }else{
                    return $this->returnjsonerror(" Account Not Allow to Create",201);
                }
            }
        }
        DB::beginTransaction();
        try{
            $chart_id = TblAccCoa::where('chart_code',$request->parent_account_code)->first('chart_account_id');
            if(isset($id)){
                $coa = TblAccCoa::where('chart_account_id',$id)->where(Utilities::currentBC())->first();
                if(isset($request->parent_account_id)){ $coa->parent_account_id = $request->parent_account_id; }
                if(isset($request->parent_account_code)){ $coa->parent_account_code =  $request->parent_account_code; }
                $coa->update_id = Utilities::uuid();
            }else{
                $coa = new TblAccCoa();
                $coa->chart_account_id = Utilities::uuid();
                $coa->chart_level = $request->chart_level;
                $coa->chart_group = ($request->chart_level < 4)?'G':'D';
                $coa->parent_account_code = $request->parent_account_code;
                $coa->parent_account_id = $chart_id->chart_account_id;
            }
            $form_id = $coa->chart_account_id;
            $coa->chart_name = $request->name;
            $coa->chart_code = $request->chart_code;
            $coa->chart_reference_code = $request->reference_code;
            $coa->chart_branch_id = (isset($request->chart_branch_id) && !empty($request->chart_branch_id))?current($request->chart_branch_id):auth()->user()->branch_id;
            $coa->chart_can_sale = isset($request->chart_can_sale)?"1":"0";
            $coa->chart_can_purchase = isset($request->chart_can_purchase)?"1":"0";
            $coa->chart_debit_limit = $request->chart_debit_limit;
            $coa->chart_credit_limit = $request->chart_credit_limit;
            $coa->chart_warn = isset($request->chart_warn)?"1":"0";
            $coa->chart_block_transaction = isset($request->chart_block_transaction)?"1":"0";
            $coa->chart_sale_expense_account = isset($request->chart_sale_expense_account)?"1":"0";
            $coa->chart_purch_expense_account = isset($request->chart_purchase_expense_account)?"1":"0";
            $coa->chart_account_entry_status = "1";
            $coa->pos_default = isset($request->pos_default)?1:0;
            $coa->pos_show = isset($request->pos_default)?1:0;
            $coa->business_id = auth()->user()->business_id;
            $coa->company_id = auth()->user()->company_id;
            $coa->branch_id = auth()->user()->branch_id;
            $coa->chart_account_user_id = auth()->user()->id;
            $coa->save();

            // chart branches
            if(isset($id)){
                TblAccCoaBranches::where('chart_id',$id)->delete();
            }
            if(isset($request->chart_branch_id)){
                foreach($request->chart_branch_id as $branch){
                    $chart_branch = new TblAccCoaBranches();
                    $chart_branch->pk_id = Utilities::uuid();
                    $chart_branch->chart_id = $coa->chart_account_id;
                    $chart_branch->branch_id = $branch;
                    $chart_branch->pos_default = isset($request->pos_default)?1:0;
                    $chart_branch->pos_default = 0;
                    $chart_branch->save();
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
            return $this->jsonErrorResponse($data, $e->getLine(), 200);
        }
        $data['name'] = "[".$coa->chart_code."] ". $coa->chart_name;
        $data['main_id'] = $coa->chart_account_id;
        $data['parent_main_id'] = $coa->parent_account_id;
        $data['level'] = $coa->chart_level;
        $data['code'] = $coa->chart_code;
        DB::commit();
        if(isset($id)){
            $data = array_merge($data, Utilities::returnJsonEditForm());
            $data['redirect'] = $this->prefixIndexPage.self::$redirect_url;
            return $this->jsonSuccessResponse($data, trans('message.update'), 200);
        }else{
            $data = array_merge($data, Utilities::returnJsonNewForm());
            $maxcode = $this->coaDisplayMaxData($coa->chart_level,$coa->parent_account_code);
            $codemax = json_encode($maxcode);
            $decode_code = json_decode($codemax, true);
            $data['maxcode']=$decode_code['original'];
            Session::put('lastData', ['chart_level' => $coa->chart_level, 'parent_account_code' => $coa->parent_account_code, 'maxcode'=>$data['maxcode'] ]);
            $data['redirect'] = '/'.self::$redirect_url.$this->prefixCreatePage.'/'.$form_id;
            return $this->jsonSuccessResponse($data, trans('message.create'), 200);
        }
    }

    public function coaDisplayData($radioValue)
    {
        $columns = TblAccCoa::select('chart_code','chart_name')
        ->where('chart_level', '=', $radioValue-1)
        ->where(Utilities::currentBCB())
        ->get();
        return response()->json($columns);
    }

    public function coaDisplayMaxData($radioValue,$parent_account_code)
    {
      // dd($radioValue."--".$parent_account_code);
//        $columns = DB::table('')->select('call get_account_code(?,?)', array($radioValue,$parent_account_code));
        $columns =  collect(DB::select('SELECT get_account_code(?,?) AS code from dual', [$radioValue,$parent_account_code]))->first()->code;
        return response()->json($columns);

    }

    public function getMaxAccountCode(Request $request,$level,$parent_account_code)
    {
        $chartAccountId = $request->chart_account_id;
        $data = [];
        $data['parent_account_id'] = TblAccCoa::where('chart_code' , $parent_account_code)->select('parent_account_id')->first();
        $checkExist = TblAccCoa::where('chart_account_id' , $chartAccountId)->where('parent_account_code' , $parent_account_code);
        if($checkExist->exists()){
            $data['new_code'] = $checkExist->first()->chart_code;
        }else{
            $data['new_code'] =  collect(DB::select('SELECT get_account_code(?,?) AS code from dual', [$level,$parent_account_code]))->first()->code;
        }
        return response()->json($data);
    }

    public function coaDisplayTaxData($tax_type_id)
    {

        $columns = TblAccTaxType::select('tax_type_percent')->where('tax_type_id', '=', $tax_type_id)->where(Utilities::currentBC())->first();
        //dd($columns->toArray());
        return response()->json($columns);

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
            $Child ='';
            $data['current'] = TblAccCoa::where('chart_account_id',$id)->where(Utilities::currentBC())->first();

            /*
                $chart_supplier_group = TblAccCoa::where('chart_Account_id',Session::get('dataSession')->supplier_group)->where(Utilities::currentBC())->first('chart_code');
                $chart_customer_group = TblAccCoa::where('chart_Account_id',Session::get('dataSession')->customer_group)->where(Utilities::currentBC())->first('chart_code');
                $supplier_group = substr($chart_supplier_group->chart_code,0,4);
                $customer_group = substr($chart_customer_group->chart_code,0,4);
            * */


            $chart_supplier_group = TblAccCoa::where('chart_account_id',1467122031700)->first();
            $chart_customer_group = TblAccCoa::where('chart_account_id',66371522121954)->first();

            //start Supplier and customer accounts cannot delete direct
            $supplier_group = substr($chart_supplier_group->chart_code,0,4);
            $customer_group = substr($chart_customer_group->chart_code,0,4);
            if(isset($data['current']->chart_code)){
                $code = substr($data['current']->chart_code,0,4);
                if($code == $supplier_group || $code == $customer_group){
                    return $this->returnjsonerror(" Account not allow to delete",201);
                }
            }
            //end Supplier and customer accounts cannot delete direct

            //--------------check child----------------------
            if($data['current']->chart_level ==1){
                $code = substr($data['current']->chart_code,0,2);
                $Child = TblAccCoa::where('chart_code','like',$code.'%')->where('chart_level', '>', $data['current']->chart_level)->where(Utilities::currentBC())->first();
            }
            if($data['current']->chart_level ==2){
                $code = substr($data['current']->chart_code,0,4);
                $Child= TblAccCoa::where('chart_code','like',$code.'%')->where('chart_level', '>', $data['current']->chart_level)->where(Utilities::currentBC())->first();
            }
            if($data['current']->chart_level ==3){
                $code = substr($data['current']->chart_code,0,7);
                $Child = TblAccCoa::where('chart_code','like',$code.'%')->where('chart_level', '>', $data['current']->chart_level)->where(Utilities::currentBC())->first();
            }
            if($data['current']->chart_level ==4){
                $Child = TblAccCoa::where('chart_code','like',$data['current']->chart_code.'%')->where('chart_level', '>', $data['current']->chart_level)->where(Utilities::currentBC())->first();
            }

           //---------------check data in other tables------------------------
           $CheckBook = TblAccoChequeBook::where('chart_account_id',$id)->where(Utilities::currentBC())->first();
           $Budget = TblAccBudget::where('chart_account_id',$id)->where(Utilities::currentBC())->first();
           $SOExp = TblSaleSalesOrderExpense::where('chart_account_id',$id)->where(Utilities::currentBC())->first();
           $SaleExp = TblSaleSalesExpense::where('chart_account_id',$id)->where(Utilities::currentBC())->first();
           $GrnExp = TblPurcGrnExpense::where('chart_account_id',$id)->where(Utilities::currentBC())->first();
           $Voucher = TblAccoVoucher::where('chart_account_id',$id)->where(Utilities::currentBC())->first();

           //----------------------------delete chart code---------------------------------------
           if($Child == null && $Voucher == null && $GrnExp == null && $SaleExp == null && $SOExp == null && $Budget == null && $CheckBook == null)
           {
                $coa = TblAccCoa::where('chart_account_id',$id)->where(Utilities::currentBC())->first();
                $coa->chart_branches()->delete();
                $coa->delete();
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
