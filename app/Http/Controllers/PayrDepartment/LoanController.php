<?php

namespace App\Http\Controllers\PayrDepartment;

use App\Http\Controllers\Controller;
use App\Library\Utilities;
use App\Models\TblHrAdvanceType;
use App\Models\TblHrLoanInstallmentDtl;
use App\Models\TblHrLoan;
use App\Models\TblHrAllowanceDeductionType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\TblHrLoanConfiguration;


use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Validator;

use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class LoanController extends Controller
{
    public static $page_title = 'Loan ';
    public static $redirect_url = 'loan';
    // Local
    // public static $menu_dtl_id = '193';
    // Live
    public static $menu_dtl_id = '194';
    public static $document_type = 'loan';

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
        $data['page_data'] = [];
        $data['page_data']['title'] = self::$page_title;
        $data['page_data']['path_index'] = $this->prefixIndexPage.self::$redirect_url;
        $data['page_data']['create'] = '/'.self::$redirect_url.$this->prefixCreatePage;
        if(isset($id)){
            if(TblHrLoan::where('loan_id','LIKE',$id)->exists()){
                $data['permission'] = self::$menu_dtl_id.'-edit';
                $data['page_data'] = array_merge($data['page_data'], Utilities::editForm());
                $data['id'] = $id;
                $data['current'] = TblHrLoan::with("loan_installment_dtl")->where('loan_id',$id)->where(Utilities::currentBCB())->first();
             // dd($data['current']);
            }else{
                abort('404');
            }
        }else{
            $data['permission'] = self::$menu_dtl_id.'-create';
            $data['page_data'] = array_merge($data['page_data'], Utilities::newForm());
        }
        $data['loan_type'] = TblHrAdvanceType::get();
       
        return view('PayrDepartment.loan.form',compact('data'));

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request,$id = null)
    {
       //dd($request->all());
         // dd($request->loan_installment_dtl); 
        $data = [];
        $validator = Validator::make($request->all(), [
            //'department' => 'required|max:255',
           // 'loan_type' => 'required|numeric',
        ]);
        if ($validator->fails()) {
            $data['validator_errors'] = $validator->errors();
            return $this->jsonErrorResponse($data, trans('message.required_fields'), 422);
        }
        DB::beginTransaction();
        try{
            if(isset($id)){
                $loan = TblHrLoan::where('loan_id',$id)->first();
            }else{
                $loan = new TblHrLoan();
                $loan->loan_id =  Utilities::uuid();
            }
            $form_id = $loan->loan_id;
            $loan->employee_id= $request->employee_id;
            $loan->description =  $request->loan_confi_name;
            $loan->loan_type =  $request->loan_confi_type_name;
            $loan->department =  $request->department;
            $loan->designation =  $request->designation;
            $loan->loan_date =  $request->loan_date;
            $loan->loan_date = date('Y-m-d', strtotime($request->loan_date));
            $loan->loan_start_date =  date('Y-m-d', strtotime($request->loan_start_date)); 
            $loan->loan_end_date =  date('Y-m-d', strtotime($request->loan_end_date)); 
            $loan->loan_amount =  $request->loan_amount;
            $loan->installment_amount =  $request->installment_amount;
            $loan->installment_no =  $request->installment_no;
            $loan->loan_deduction =  $request->loan_deduction;
            $loan->loan_paid =  $request->loan_paid;
            $loan->balance_loan =  $request->balance_loan;
            $loan->remarks =  $request->remarks;
            $loan->business_id = auth()->user()->business_id;
            $loan->company_id = auth()->user()->company_id;
            $loan->branch_id = auth()->user()->branch_id;
            $loan->loan_user_id = auth()->user()->id;
            $loan->save(); 
      
            if (isset($id)) {
                $loan_dlt=  TblHrLoanInstallmentDtl::where('loan_installment_id', $id)->delete();
               
            }
            if (isset($request->pd)) {
               
                foreach ($request->pd as $pd) {
                    $loan_dlt = new TblHrLoanInstallmentDtl();
                    if (isset($pd['loan_installment_id'])) {
                        $loan_dlt->loan_installment_id = $pd['loan_installment_id'];
                        $loan_dlt->loan_installment_id = $id;
                    } else {
                        $loan_dlt->loan_installment_id = Utilities::uuid();
                        $loan_dlt->loan_installment_id = $loan_dlt->loan_installment_id;
                    }
                    $loan_dlt->loan_id = $form_id;
                    $loan_dlt->date = $pd['date']; 
                    $loan_dlt->per_installment_amount = $pd['per_installment_amount']; 
                    $loan_dlt->paid_amount = $pd['paid_amount']; 
                    $loan_dlt-> balance_amount = $pd['balance_amount'];                   
                   $loan_dlt->business_id = auth()->user()->business_id;
                    $loan_dlt->company_id = auth()->user()->company_id;
                    $loan_dlt->branch_id = auth()->user()->branch_id;
                    $loan_dlt->loan_installment_user_id = auth()->user()->id;
                    $loan_dlt->save();
                }
            }

        }
        catch (QueryException $e) {
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
                $loan =TblHrLoan ::where('loan_id',$id)->first();
                $loan->delete();
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
