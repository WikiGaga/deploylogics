<?php

namespace App\Http\Controllers\PayrDepartment;

use App\Http\Controllers\Controller;
use App\Library\Utilities;
use App\Models\TblHrAllowanceDeductionType;
use App\Models\TblHrPayrollComputation;
use App\Models\TblHrPayrollCompoutationAllowance;
use App\Models\TblHrPayrollDeduction;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;

use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Validator;

use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class PayrollComputation extends Controller
{
    public static $page_title = 'Payroll Computation';
    public static $redirect_url = 'payroll-computation';
    public static $menu_dtl_id= '67';
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
        $data['page_data']['path_index'] = $this->prefixIndexPage.self::$redirect_url;;
        if(isset($id)){
            if(TblHrPayrollComputation::where('payroll_computation_id','LIKE',$id)->exists()){
                $data['page_data'] = array_merge($data['page_data'], Utilities::editForm());
                $data['permission'] = self::$menu_dtl_id.'-edit';
                $data['id'] = $id;
                $data['current'] = TblHrPayrollComputation::with('payroll_allowance','payroll_deduction')->where('payroll_computation_id',$id)->first();
                 // dd($data['current']);
            }else{
                abort('404');
            }
        }else{
            $data['permission'] = self::$menu_dtl_id.'-create';
            $data['page_data'] = array_merge($data['page_data'], Utilities::newForm());
        }
      
        return view('PayrDepartment.payroll_computation.form',compact('data'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request,$id = null)
    {
   // dd($request->all());
     //dd($request->deduct_repeater_list); 
        $data = [];
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255',
            
        ]);
        if ($validator->fails()) {
            $data['validator_errors'] = $validator->errors();
            return $this->jsonErrorResponse($data, trans('message.required_fields'), 422);
        }
        DB::beginTransaction();
        try{
            if(isset($id)){
                $payroll_computation = TblHrPayrollComputation::where('payroll_computation_id',$id)->first();
            }else{
                $payroll_computation = new TblHrPayrollComputation();
             
                $payroll_computation->payroll_computation_id =  Utilities::uuid();
            }
            $form_id = $payroll_computation->payroll_computation_id;     
            $payroll_computation->payroll_computation_name =  $request->name;   
            $payroll_computation->payroll_computation_date = date('Y-m-d', strtotime($request->date));

          //  $payroll_computation->payroll_computation_date =  $request->date; 
            $payroll_computation->payroll_computation_entry_status = isset($request->entry_status)?"1":"0";
            $payroll_computation->business_id = auth()->user()->business_id;
            $payroll_computation->company_id = auth()->user()->company_id;
            $payroll_computation->branch_id = auth()->user()->branch_id;
            $payroll_computation->payroll_computation_user_id = auth()->user()->id;
            $payroll_computation->save(); 

            if (isset($id)) {
                $payroll_allowance=  TblHrPayrollCompoutationAllowance::where('allowance_id', $id)->delete();
                //dd($payroll_allowance);
            }
            if (isset($request->repeater_list)) {
               
                foreach ($request->repeater_list as $repeater_list) {
                  
                    $payroll_allowance = new TblHrPayrollCompoutationAllowance();
                   
                    if (isset($repeater_list['allowance_id'])) {
                        $payroll_allowance->allowance_id = $repeater_list['allowance_id'];
                        $payroll_allowance->allowance_id = $id;
                    } else {
                        $payroll_allowance->allowance_id = Utilities::uuid();
                        $payroll_allowance->allowance_id = $payroll_allowance->allowance_id;
                    }
                    $payroll_allowance->payroll_computation_id = $form_id;
                
                    $payroll_allowance->allowance_salary_head = $repeater_list['n'];
                    $payroll_allowance->allowance_salary_type = $repeater_list['t'];
                    $payroll_allowance->allowance_value = $repeater_list['v'];
                    $payroll_allowance->business_id = auth()->user()->business_id;
                    $payroll_allowance->company_id = auth()->user()->company_id;
                    $payroll_allowance->branch_id = auth()->user()->branch_id;
                    $payroll_allowance->allowance_user_id = auth()->user()->id;
                  
                    $payroll_allowance->save();
                }
            }
            if (isset($id)) {
                $payroll_deduction= TblHrPayrollDeduction::where('deduction_id', $id)->delete();
            }
            if (isset($request->deduct_repeater_list)) {
               
                foreach ($request->deduct_repeater_list as $deduct_repeater_list) {
                    $payroll_deduction = new TblHrPayrollDeduction();
                    if (isset($deduct_repeater_list['deduction_id'])) {
                        $payroll_deduction->deduction_id = $deduct_repeater_list['deduction_id'];
                        $payroll_deduction->deduction_id = $id;
                    } else {
                        $payroll_deduction->deduction_id = Utilities::uuid();
                        $payroll_deduction->deduction_id = $payroll_deduction->deduction_id;
                    }
                    $payroll_deduction->payroll_computation_id = $form_id;
                  // dd($payroll_deduction);
                    $payroll_deduction->deduction_salary_head = $deduct_repeater_list['n'];
                    $payroll_deduction->deduction_salary_type = $deduct_repeater_list['t'];
                    $payroll_deduction->deduction_value = $deduct_repeater_list['v'];
                    $payroll_deduction->business_id = auth()->user()->business_id;
                    $payroll_deduction->company_id = auth()->user()->company_id;
                    $payroll_deduction->branch_id = auth()->user()->branch_id;
                    $payroll_deduction->deduction_user_id = auth()->user()->id;
                    $payroll_deduction->save();
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

    public function calculateFormula(Request $request)
    {
        $data = $request->all();
        $data['tags'] = TblHrAllowanceDeductionType::get();
        $view = view('PayrDepartment.payroll_computation.formula_modal', compact('data'))->render();
        return response()->json(['body'=>$view]);
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
                $payroll_computation=TblHrPayrollComputation::where('payroll_computation_id',$id)->first();
                $payroll_computation->payroll_allowance()->delete();
                $payroll_computation->payroll_deduction()->delete();
                $payroll_computation->delete();

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
