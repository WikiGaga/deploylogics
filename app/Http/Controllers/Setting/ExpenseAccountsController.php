<?php

namespace App\Http\Controllers\Setting;

use App\Http\Controllers\Controller;
use App\Library\Utilities;
use App\Models\Settings\TblDefiExpenseAccounts;
use App\Models\TblDefiConfigBranches;
use App\Models\TblDefiConfiguration;
use App\Models\TblAccCoa;
use App\Models\TblDefiShortcutKeys;
use App\Models\TblSoftBranch;
use Illuminate\Http\Request;

// db and Validator
use Illuminate\Validation\Rule;
use Validator;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Session;
class ExpenseAccountsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public static $page_title = 'Expense Accounts';
    public static $redirect_url = 'expense-accounts';
    public static $menu_dtl_id = '187';

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
        $data['page_data']['path_index'] = $this->prefixIndexPage.self::$redirect_url;;
        $data['permission'] = self::$menu_dtl_id.'-create';
       // dd($data['current']->toArray());
        return view('setting.expense_accounts.form',compact('data'));
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
        $validator = Validator::make($request->all(), [

        ]);
        if ($validator->fails()) {
            $data['validator_errors'] = $validator->errors();
            return $this->jsonErrorResponse($data, trans('message.required_fields'), 200);
        }
        DB::beginTransaction();
        try{

            $d = $this->storeAccounts($request->grn_acc,'grn_acc');
            if($d == 'error'){
                return $this->jsonErrorResponse($data, trans('message.required_fields'), 200);
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
            return $this->jsonSuccessResponse($data, trans('message.update'), 200);
        }else{
            $data = array_merge($data, Utilities::returnJsonNewForm());
            $data['redirect'] = '/'.self::$redirect_url.$this->prefixCreatePage;
            return $this->jsonSuccessResponse($data, trans('message.create'), 200);
        }
    }
    public function storeAccounts($request,$type) {
       // dd($request);
        TblDefiExpenseAccounts::where('expense_accounts_type','=',$type)->where('branch_id','LIKE',auth()->user()->branch_id)->delete();
        $k = 1;
        foreach ($request as $row){
            if($row['acc_dr_cr_id'] != 'dr' && $row['acc_dr_cr_id'] != 'cr'){
                return 'error';
            }
            if($row['acc_plus_minus_id'] != '+' && $row['acc_plus_minus_id'] != '-'){
                return 'error';
            }
            $exp_acc = new TblDefiExpenseAccounts();
            $exp_acc->expense_accounts_id = Utilities::uuid();
            $exp_acc->sr_no = $k;
            $exp_acc->expense_accounts_type = $type;
            $exp_acc->chart_account_id = $row['account_id'];
            $exp_acc->expense_accounts_dr_cr = $row['acc_dr_cr_id'];
            $exp_acc->expense_accounts_plus_minus = $row['acc_plus_minus_id'];
            $exp_acc->business_id = auth()->user()->business_id;
            $exp_acc->company_id = auth()->user()->company_id;
            $exp_acc->branch_id = auth()->user()->branch_id;
            $exp_acc->expense_accounts_user_id = auth()->user()->id;
            $exp_acc->save();
            $k = $k+1;
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
        //
    }
}
