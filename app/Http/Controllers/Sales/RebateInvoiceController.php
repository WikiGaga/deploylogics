<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use App\Library\Utilities;
use App\Models\TblAccoPaymentTerm;
use App\Models\TblDefiCurrency;
use App\Models\TblPurcQuotationAccount;
use App\Models\ViewPurcSupplier;
use App\Models\ViewSaleCustomer;
use App\Models\TblSaleSales;
use App\Models\TblSaleSalesDtl;
use App\Models\TblSaleSalesExpense;
use App\Models\TblAccCoa;
use App\Models\TblSoftPosInvoiceHeadings;
use App\Models\User;
use App\Models\TblDefiPaymentType;
use Illuminate\Http\Request;
// db and Validator
use Illuminate\Validation\Rule;
use Validator;
use Illuminate\Support\Facades\DB;
use Exception;
use Session;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class RebateInvoiceController extends Controller
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
    public static $page_title = 'Rebate Invoice';
    public static $redirect_url = 'rebate-invoice';
    public static $menu_dtl_id = '190';
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
        $data['page_data']['create'] = '/'.self::$redirect_url.$this->prefixCreatePage;
        if(isset($id)){
            if(TblSaleSales::where('sales_id','LIKE',$id)->where(Utilities::currentBCB())->exists()){
                $data['permission'] = self::$menu_dtl_id.'-edit';
                $data['page_data'] = array_merge($data['page_data'], Utilities::editForm());
                $data['id'] = $id;
                $data['current']  = TblSaleSales::with('dtls','supplier_view','expense')->where(Utilities::currentBCB())->where('sales_id',$id)->first();
                $data['document_code'] = $data['current']->sales_code;
                $data['page_data']['print'] = '/'.self::$redirect_url.'/print/'.$id;
                $data['users'] = User::where('user_type','erp')->where('user_entry_status',1)->where(Utilities::currentBC())->get();
            }
            else{
                abort('404');
            }
        }else{
            $data['permission'] = self::$menu_dtl_id.'-create';
            $data['page_data'] = array_merge($data['page_data'], Utilities::newForm());
            $data['document_code'] = $this->documentCode(TblSaleSales::where(Utilities::currentBCB())->where('sales_type','RI')->max('sales_code'),'RI');
        }
        $data['users'] = User::where('user_type','erp')->where('user_entry_status',1)->where(Utilities::currentBC())->where('id',auth()->user()->id)->get();
        $data['payment_terms'] = TblAccoPaymentTerm::where(Utilities::currentBC())->get();
        $data['currency'] = TblDefiCurrency::where(Utilities::currentBC())->where('currency_entry_status',1)->get();
        $data['accounts'] = TblAccCoa::where(Utilities::currentBC())->where('chart_sale_expense_account',1)->get();
        $data['payment_type'] = TblDefiPaymentType::where(Utilities::currentBC())->where('payment_type_entry_status',1)->get();
        $arr = [
            'biz_type' => 'branch',
            'code' => $data['document_code'],
            'link' => $data['page_data']['create'],
            'table_name' => 'tbl_sale_sales',
            'col_id' => 'sales_id',
            'col_code' => 'sales_code',
            'code_type_field'   => 'sales_type',
            'code_type'         => 'RI',
        ];
        $data['switch_entry'] = $this->switchEntry($arr);
        return view('sales.rebate_invoice.form',compact('data'));
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
            'supplier_name' => 'required',
            'supplier_id' => 'required|numeric',
            'currency_id' => 'required|numeric',
            'exchange_rate' => 'required|numeric',
            'payment_term_id' => 'nullable|numeric',
            'sales_credit_days' => 'nullable|numeric',
            'sales_sales_type' => 'required|numeric',
            'sales_sales_man' => 'nullable|numeric',
            'sales_remarks' => 'nullable|max:255',
        ]);
        if ($validator->fails()) {
            $data['validator_errors'] = $validator->errors();
            return $this->jsonErrorResponse($data, trans('message.required_fields'), 200);
        }
        if(isset($request->supplier_id)){
            $exitsCustomer = ViewPurcSupplier::where('supplier_id',$request->supplier_id)->where(Utilities::currentBC())->exists();
            if (!$exitsCustomer) {
                return $this->returnjsonerror("Customer Not Exist",200);
            }
        }
        if($request->pro_tot <= 0){
            return $this->returnjsonerror("Please Enter Product Detail",200);
        }
        if(!isset(
            session::get('dataSession')->rebate_invoice_income ,
            session::get('dataSession')->rebate_invoice_discount,
            session::get('dataSession')->rebate_invoice_vat_payable,
            session::get('dataSession')->rebate_invoice_stock,
            session::get('dataSession')->rebate_invoice_stock_consumption,
            session::get('dataSession')->rebate_invoice_cash_ac
        )){
            return $this->jsonErrorResponse($data, "Please select accounts in configuration", 200);
        }
        if(isset($request->pdsm)){
            foreach($request->pdsm as $expense){
                if(!empty($expense['account_code'])){
                    $exits = TblAccCoa::where('chart_account_id',$expense['account_id'])->where('chart_code',$expense['account_code'])->where(Utilities::currentBC())->exists();
                    if (!$exits) {
                        return $this->returnjsonerror(" Account Code not correct",201);
                    }
                }else{
                    return $this->returnjsonerror(" Enter Account Code",201);
                }
            }
        }
        DB::beginTransaction();
        try{
            if(isset($id)){
                $sales = TblSaleSales::where('sales_id',$id)->where('sales_type','RI')->where(Utilities::currentBCB())->first();
            }else{
                $sales = new TblSaleSales();
                $sales->sales_id = Utilities::uuid();
                $sales->sales_code = $this->documentCode(TblSaleSales::where(Utilities::currentBCB())->where('sales_type','RI')->max('sales_code'),'RI');
            }
            $form_id = $sales->sales_id;
            $sales->sales_type = 'RI';
            $sales->sales_date = date('Y-m-d', strtotime($request->sales_date));
            $sales->payment_term_id = $request->payment_term_id;
            $sales->sales_credit_days = $request->sales_credit_days;
            $sales->currency_id = $request->currency_id;
            $sales->customer_id = $request->supplier_id;
            $sales->sales_exchange_rate = $request->exchange_rate;
            $sales->sales_sales_man = $request->sales_sales_man;
            $sales->sales_sales_type = $request->sales_sales_type;
            $sales->sales_delivery_id = $request->sales_delivery_id;
            $sales->sales_mobile_no = $request->sales_mobile_no;
            $sales->sales_contract_person = $request->sales_contract_person;
            $sales->sales_remarks = $request->sales_remarks;
            $sales->sales_net_amount = $request->Total_Amount;
            $sales->sales_entry_status = 1;
            $sales->business_id = auth()->user()->business_id;
            $sales->company_id = auth()->user()->company_id;
            $sales->branch_id = auth()->user()->branch_id;
            $sales->sales_user_id = auth()->user()->id;
            $sales->save();

            $del_Dtls = TblSaleSalesDtl::where('sales_id',$id)->where(Utilities::currentBCB())->get();
            foreach ($del_Dtls as $del_Dtl){
                TblSaleSalesDtl::where('sales_dtl_id',$del_Dtl->sales_dtl_id)->where(Utilities::currentBCB())->delete();
            }
            $sr_no = 1;
            $net_total = 0;
            $amount_total = 0;
            $vat_amount_total = 0;
            $disc_amount_total = 0;
            $TotalExpAmount = 0;
            $total_net_amount = 0;
            if(isset($request->pd)){
                foreach($request->pd as $pd){
                    $dtl = new TblSaleSalesDtl();
                    if(isset($id) && isset($pd['sales_dtl_id']) && $pd['sales_dtl_id'] != 'undefined' ){
                        $dtl->sales_dtl_id = $pd['sales_dtl_id'];
                        $dtl->sales_id = $id;
                    }else{
                        $dtl->sales_dtl_id = Utilities::uuid();
                        $dtl->sales_id = $sales->sales_id;
                    }
                    $dtl->sr_no = $sr_no++;
                    $dtl->sales_type = 'RI';
                    $dtl->item_description = $pd['item_description'];
                    $dtl->purc_amount = $pd['purc_amount'];
                    $dtl->sales_dtl_start_date = date('Y-m-d', strtotime($pd['sales_dtl_start_date']));
                    $dtl->sales_dtl_end_date = date('Y-m-d', strtotime($pd['sales_dtl_end_date']));
                    $dtl->sales_dtl_quantity = $this->addNo(isset($pd['quantity'])?$pd['quantity']:'0');
                    $dtl->sales_dtl_foc_qty = $this->addNo(isset($pd['foc_qty'])?$pd['foc_qty']:'0');
                    $dtl->sales_dtl_fc_rate = $this->addNo(isset($pd['fc_rate'])?$pd['fc_rate']:'0.00');
                    $dtl->sales_dtl_rate = $this->addNo(isset($pd['rate'])?$pd['rate']:'0.00');
                    $dtl->sales_dtl_amount = $this->addNo(isset($pd['amount'])?$pd['amount']:'0.00');
                    $dtl->sales_dtl_disc_per = $this->addNo(isset($pd['dis_perc'])?$pd['dis_perc']:'0.00');
                    $dtl->sales_dtl_disc_amount = $this->addNo(isset($pd['dis_amount'])?$pd['dis_amount']:'0.00');
                    $dtl->sales_dtl_vat_per = $this->addNo(isset($pd['vat_perc'])?$pd['vat_perc']:'0.00');
                    $dtl->sales_dtl_vat_amount = $this->addNo(isset($pd['vat_amount'])?$pd['vat_amount']:'0.00');
                    $dtl->sales_dtl_total_amount = $this->addNo(isset($pd['gross_amount'])?$pd['gross_amount']:'0.00');
                    $dtl->sales_dtl_gross_rate = $this->addNo(isset($pd['g_rate'])?$pd['g_rate']:'0.00');
                    $dtl->business_id = auth()->user()->business_id;
                    $dtl->company_id = auth()->user()->company_id;
                    $dtl->branch_id = auth()->user()->branch_id;
                    $dtl->sales_dtl_user_id = auth()->user()->id;
                    $dtl->save();

                    $net_total += $this->addNo($pd['gross_amount']);
                    $total_net_amount += $this->addNo($pd['gross_amount']);
                    $amount_total += $this->addNo($pd['amount']);
                    $vat_amount_total += $this->addNo(isset($pd['vat_amount'])?$pd['vat_amount']:'0.00');
                    $disc_amount_total += $this->addNo(isset($pd['dis_amount'])?$pd['dis_amount']:'0.00');
                }
            }
            //dd($dtl->toArray());
            if(isset($id)){
                $del_Dtls = TblSaleSalesExpense::where('sales_id',$id)->get();
                foreach ($del_Dtls as $del_Dtls){
                    TblSaleSalesExpense::where(Utilities::currentBCB())->where('sales_expense_id',$del_Dtls->sales_expense_id)->delete();
                }
            }
            if(isset($request->pdsm)){
                foreach($request->pdsm as $expense){
                    if(isset($expense['expense_amount'])){
                        $expenseDtl = new TblSaleSalesExpense();
                        $expenseDtl->sales_expense_id = Utilities::uuid();
                        if(isset($id)){
                            $expenseDtl->sales_id = $id;
                        }else{
                            $expenseDtl->sales_id = $sales->sales_id;
                        }
                        $expenseDtl->chart_account_id = $expense['account_id'];
                        $expenseDtl->sales_expense_account_code = $expense['account_code'];
                        $expenseDtl->sales_expense_account_name = $expense['account_name'];
                        $expenseDtl->sales_expense_amount = $this->addNo($expense['expense_amount']);
                        $expenseDtl->business_id = auth()->user()->business_id;
                        $expenseDtl->company_id = auth()->user()->company_id;
                        $expenseDtl->branch_id = auth()->user()->branch_id;
                        $expenseDtl->sales_expense_user_id = auth()->user()->id;
                        $expenseDtl->save();
                        $net_total += $this->addNo($expense['expense_amount']);
                        $TotalExpAmount += $this->addNo($expense['expense_amount']);
                    }
                }
            }

            $saleTotal = TblSaleSales::where('sales_id',$sales->sales_id)->where(Utilities::currentBCB())->first();
            $saleTotal->total_expense = $TotalExpAmount;
            $saleTotal->sales_net_amount = $total_net_amount - $TotalExpAmount;
            $saleTotal->save();
            // insert update sale voucher
            $table_name = 'tbl_acco_voucher';
            if(isset($id)){
                $action = 'update';
                $sale_id = $id;
                $sale = TblSaleSales::where('sales_id',$sale_id)->first();
                $voucher_id = $sale->voucher_id;
            }else{
                $action = 'add';
                $sale_id = $sales->sales_id;
                $voucher_id = Utilities::uuid();
            }
            $where_clause = '';
            $customer = ViewPurcSupplier::where('supplier_id',$request->supplier_id)->first();

            if($request->sales_sales_type == 1){
                $customer_chart_account_id = (int)Session::get('dataSession')->display_rent_fee_cash_ac;
            }else{
                $customer_chart_account_id = (int)$customer->supplier_account_id;
            }
            //check account code
            $ChartArr = [
                $customer_chart_account_id,
                session::get('dataSession')->rebate_invoice_discount,
                session::get('dataSession')->rebate_invoice_income,
                session::get('dataSession')->rebate_invoice_vat_payable
            ];
            $response = $this->ValidateCharCode($ChartArr);
            if($response == false){
                return $this->returnjsonerror("voucher Account Code not correct",404);
            }
            //voucher start
            $data = [
                'voucher_id'            =>  $voucher_id,
                'voucher_document_id'   =>  $sale_id,
                'voucher_no'            =>  $sales->sales_code,
                'voucher_date'          =>  date('Y-m-d', strtotime($request->sales_date)),
                'voucher_descrip'       =>  'Rebate Inv.: '.$sales->sales_remarks,
                'voucher_type'          =>  'RI',
                'branch_id'             =>  auth()->user()->branch_id,
                'business_id'           =>  auth()->user()->business_id,
                'company_id'            =>  auth()->user()->company_id,
                'voucher_user_id'       =>  auth()->user()->id
            ];
            $only_amount = $net_total - $vat_amount_total;
            $data['chart_account_id'] = $customer_chart_account_id;
            $data['voucher_debit'] = abs($only_amount);
            $data['voucher_credit'] = 0;
            $data['voucher_sr_no'] = 1;
            // for debit entry net_total
            $this->proAccoVoucherInsert($voucher_id,$action,$table_name,$data,$where_clause);

            $action = 'add';
            $data['chart_account_id'] = $customer_chart_account_id;
            $data['voucher_debit'] = abs($vat_amount_total);
            $data['voucher_credit'] = 0;
            $data['voucher_sr_no'] = 2;
            $data['voucher_descrip'] = 'Vat on Rebate Inv.: '.$sales->sales_remarks;
            // for debit entry net_total
            $this->proAccoVoucherInsert($voucher_id,$action,$table_name,$data,$where_clause);

            $action = 'add';
            $discount_chart_account_id = Session::get('dataSession')->display_rent_fee_discount;
            $data['chart_account_id'] = $discount_chart_account_id;
            $data['voucher_debit'] = abs($disc_amount_total);
            $data['voucher_credit'] = 0;
            $data['voucher_sr_no'] = 3;
            $data['voucher_descrip'] = 'Discount on Rebate Inv.: '.$sales->sales_remarks;
            // for debit entry disc_amount_total
            $this->proAccoVoucherInsert($voucher_id,$action,$table_name,$data,$where_clause);

            $action = 'add';
            $income_chart_account_id = Session::get('dataSession')->display_rent_fee_income;
            $data['chart_account_id'] = $income_chart_account_id;
            $data['voucher_debit'] = 0;
            $data['voucher_credit'] = (abs($amount_total) + abs($vat_amount_total));
            $data['voucher_sr_no'] = 4;
            $data['voucher_descrip'] = 'Income on Rebate Inv.: '.$sales->sales_remarks;
            // for credit entry amount_total
            $this->proAccoVoucherInsert($voucher_id,$action,$table_name,$data,$where_clause);

            $action = 'add';
            $income_chart_account_id = Session::get('dataSession')->display_rent_fee_income;
            $data['chart_account_id'] = $income_chart_account_id;
            $data['voucher_debit'] = abs($vat_amount_total);
            $data['voucher_credit'] = 0;
            $data['voucher_sr_no'] = 5;
            $data['voucher_descrip'] = 'Income on Rebate Inv.: '.$sales->sales_remarks;
            // for credit entry amount_total
            $this->proAccoVoucherInsert($voucher_id,$action,$table_name,$data,$where_clause);

            $action = 'add';
            $vat_payable_chart_account_id = Session::get('dataSession')->display_rent_fee_vat_payable;
            $data['chart_account_id'] = $vat_payable_chart_account_id;
            $data['voucher_debit'] = 0;
            $data['voucher_credit'] = abs($vat_amount_total);
            $data['voucher_sr_no'] = 6;
            $data['voucher_descrip'] = 'Vat Payable on Rebate Inv.: '.$sales->sales_remarks;
            // for credit entry vat_amount_total
            $this->proAccoVoucherInsert($voucher_id,$action,$table_name,$data,$where_clause);

            if(isset($request->pdsm)){
                $sr_no = 7;
                foreach($request->pdsm as $expense){
                    if(0 < $this->addNo($expense['expense_amount'])){
                        $data['voucher_debit'] = 0;
                        $data['voucher_credit'] = abs($expense['expense_amount']);
                    }else{
                        $data['voucher_debit'] = abs($expense['expense_amount']);
                        $data['voucher_credit'] = 0;
                    }
                    $action = 'add';
                    $data['chart_account_id'] = $expense['account_id'];
                    $data['voucher_sr_no'] = $sr_no;
                    $data['voucher_descrip'] = 'Expense on Rebate Inv.: '.$sales->sales_remarks;
                    $this->proAccoVoucherInsert($voucher_id,$action,$table_name,$data,$where_clause);
                    $sr_no++;
                }
            }

            if(!isset($id)){
                $sale = TblSaleSales::where('sales_id',$sale_id)->where(Utilities::currentBCB())->first();
                $sale->voucher_id = $voucher_id;
                $sale->save();
            }
            // end insert update sale voucher

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

    /***
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


    public function print($id,$type=null)
    {

        $data['title'] = 'Rebate Invoice';
        $data['type'] = $type;
        $data['permission'] = self::$menu_dtl_id.'-print';
        $data['print_link'] = '/'.self::$redirect_url.'/print/'.$id.'/pdf';
        if(isset($id)){
            if(TblSaleSales::where('sales_id','LIKE',$id)->where(Utilities::currentBCB())->exists()){
                $data['current'] = TblSaleSales::with('dtls','customer_view','expense','SO','sales_contract')->where(Utilities::currentBCB())->where('sales_id',$id)->where('sales_type','RI')->first();
            }else{
                abort('404');
            }
        }
        $data['users'] = User::where('user_type','erp')->where('user_entry_status',1)->where(Utilities::currentBC())->where('id',$data['current']->sales_sales_man)->first();
        $data['payment_terms'] = TblAccoPaymentTerm::where('payment_term_id',$data['current']->payment_term_id)->where(Utilities::currentBC())->where('payment_term_entry_status',1)->first();
        $data['currency'] = TblDefiCurrency::where('currency_id',$data['current']->currency_id)->where(Utilities::currentBC())->first();
        $data['payment_type'] = TblDefiPaymentType::where('payment_type_entry_status',1)->where(Utilities::currentBC())->where('payment_type_id',$data['current']->sales_sales_type)->first();
        $data['invoice_headings'] = TblSoftPosInvoiceHeadings::pluck('heading_arabic_name','heading_key');

        return view('prints.rebate_invoice_print',compact('data'));
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

            $sales = TblSaleSales::where('sales_id',$id)->where(Utilities::currentBCB())->where('sales_type','RI')->first();
            $voucher_id = $sales->voucher_id;
            if(!empty($voucher_id)){
                $this->proAccoVoucherDelete($voucher_id);
            }

            $sales->dtls()->delete();
            $sales->expense()->delete();
            $sales->delete();

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
