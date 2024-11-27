<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use App\Library\Utilities;
use App\Models\TblAccoPaymentTerm;
use App\Models\TblDefiCurrency;
use App\Models\TblPurcQuotationAccount;
use App\Models\TblSaleSales;
use App\Models\TblSaleSalesDtl;
use App\Models\TblSaleCustomer;
use App\Models\User;
use App\Models\TblAccCoa;
use App\Models\TblSaleSalesExpense;
use App\Models\TblDefiPaymentType;
use App\Models\ViewSaleCustomer;
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

class SaleReturnNoAuthController extends Controller
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

    public static $page_title = 'Sale Return';
    public static $redirect_url = 'sale-return-noauth';
    public static $menu_dtl_id = '51';
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($id = null)
    {

        $branch_branch_id = 2; // Mulada Branch

        $data['branch_branch_id'] = $branch_branch_id;

        $currentBCB = [
            ['business_id',1],
            ['company_id',1],
            ['branch_id',$branch_branch_id]
        ];

        $currentBC = [
            ['business_id',1],
            ['company_id',1]
        ];


        $x = parse_url($_SERVER['REQUEST_URI']);
        $type = explode('/',$x['path']);

        switch ($type[1]){
            case 'pos-sales-return': {
                $data['page_data']['title'] = 'POS Sales Return';
                $data['form_type'] = 'pos-sales-return';
                $data['invoice_menu_id'] = '164';
                break;
            }
            case 'sale-return': {
                $data['page_data']['title'] = 'Sales Return';
                $data['form_type'] = 'sale-return';
                $data['invoice_menu_id'] = '51';
                $data['page_data']['create'] = '/'.$data['form_type'].$this->prefixCreatePage;
                break;
            }
            case 'sale-return-noauth': {
                $data['page_data']['title'] = 'Sales Return';
                $data['form_type'] = 'sale-return';
                $data['invoice_menu_id'] = '51';
                $data['page_data']['create'] = '/'.$data['form_type'].$this->prefixCreatePage;
                break;
            }
        }
        if(!isset($data['form_type'])){
            abort('404');
        }
        $data['page_data']['title'] = self::$page_title;
        $data['page_data']['path_index'] = $this->prefixIndexPage.$data['form_type'];
        if(isset($id)){
            if(TblSaleSales::where('sales_id','LIKE',$id)->where($currentBCB)->exists()){
                $data['permission'] = $data['invoice_menu_id'].'-edit';
                $data['page_data'] = array_merge($data['page_data'], Utilities::editForm());
                $data['id'] = $id;
                $qry = TblSaleSales::with('dtls','customer_view','expense','SO')->where($currentBCB)->where('sales_id',$id);
                if($data['form_type'] == 'sale-return'){
                    $qry = $qry->where('sales_type','SR')->first();
                }
                if($data['form_type'] == 'pos-sales-return'){
                    $qry = $qry->where('sales_type','RPOS')->first();
                }
                $data['current'] = $qry;
                if($data['form_type'] == 'pos-sales-return'){
                    if(empty($data['current']->sales_sales_man)){
                        $data['pos_user'] = User::where('id',$data['current']->sales_user_id)->first();
                    }else{
                        $data['pos_user'] = User::where('id',$data['current']->sales_sales_man)->first();
                    }
                }
                $data['document_code'] = $data['current']->sales_code;
                $data['page_data']['print'] = '/'.self::$redirect_url.'/print/'.$id;
            }else{
                abort('404');
            }
        }else{
            $data['permission'] = $data['invoice_menu_id'].'-create';
            $data['page_data'] = array_merge($data['page_data'], Utilities::newForm());
            $doc_data = [
                'biz_type'          => 'branch',
                'model'             => 'TblSaleSales',
                'code_field'        => 'sales_code',
                'code_prefix'       => strtoupper('sr'),
                'code_type_field'   => 'sales_type',
                'code_type'         => strtoupper('sr'),
            ];
            $data['document_code'] = Utilities::documentCode($doc_data);
            $data['customer'] = TblSaleCustomer::select(['customer_id','customer_name'])->where('customer_default_customer',1)->where($currentBC)->first();
        }
        $data['users'] = User::where('user_type','erp')->where('user_entry_status',1)->where($currentBC)->where('id',81)->get();
        $data['payment_terms'] = TblAccoPaymentTerm::where($currentBC)->get();
        $data['currency'] = TblDefiCurrency::where('currency_entry_status',1)->where($currentBC)->get();
        $data['accounts'] = TblAccCoa::where('chart_sale_expense_account',1)->where($currentBC)->get();

        $data['payment_type'] = TblDefiPaymentType::where('payment_type_entry_status',1)
            ->where($currentBC)->get();
        $data['rate_types'] = config('constants.rate_type');
        if($data['form_type'] == 'sale-return'){
            $arr = [
                'biz_type' => 'business',
                'code' => $data['document_code'],
                'link' => $data['page_data']['create'],
                'table_name' => 'tbl_sale_sales',
                'col_id' => 'sales_id',
                'col_code' => 'sales_code',
                'code_type_field'   => 'sales_type',
                'code_type'         => strtoupper('sr'),
            ];
            // $data['switch_entry'] = $this->switchEntry($arr);
        }
 // dd($data['users']->toArray());
        return view('sales.sale_return.formna',compact('data'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $id = null)
    {
        $sessionSaleReturnCashAc = 60;
        $sessionSaleReturnDiscount = 206;
        $sessionSaleReturnIncome = 208;
        $sessionSaleReturnVatPayable = 17215620301051;
        $branch_branch_id = 2; // Mulada Branch

        $data['branch_branch_id'] = $branch_branch_id;

        $currentBCB = [
            ['business_id',1],
            ['company_id',1],
            ['branch_id',$branch_branch_id]
        ];

        $currentBC = [
            ['business_id',1],
            ['company_id',1]
        ];

        
        $data = [];
        $validator = Validator::make($request->all(), [
            'customer_name' => 'required',
            'customer_id' => 'required|numeric',
            'currency_id' => 'required|numeric',
            'exchange_rate' => 'required|numeric',
            'payment_term_id' => 'nullable|numeric',
            'sales_credit_days' => 'nullable|numeric',
            'sales_sales_type' => 'required|numeric',
            'sales_sales_man' => 'required|numeric',
            'sales_remarks' => 'nullable|max:255',
            'pd.*.product_id' => 'nullable|numeric',
            'pd.*.product_barcode_id' => 'nullable|numeric',
        ]);
        if ($validator->fails()) {
            $data['validator_errors'] = $validator->errors();
            return $this->jsonErrorResponse($data, trans('message.required_fields'), 422);
        }
        if(isset($request->customer_id)){
            $exitsCustomer = ViewSaleCustomer::where('customer_id',$request->customer_id)->where($currentBC)->exists();
            if (!$exitsCustomer) {
                return $this->returnjsonerror("Customer Not Exist",201);
            }
        }
        /*if($request->pro_tot <= 0){
            return $this->returnjsonerror("Please Enter Product Detail",201);
        }*/
        if(isset($request->pdsm)){
            foreach($request->pdsm as $expense){
                if(!empty($expense['account_code'])){
                    $exits = TblAccCoa::where('chart_account_id',$expense['account_id'])->where('chart_code',$expense['account_code'])->where($currentBC)->exists();
                    if (!$exits) {
                        return $this->returnjsonerror(" Account Code  not correct",201);
                    }
                }else{
                    return $this->returnjsonerror(" Enter Acount Code",201);
                }
            }
        }
        $x = parse_url($_SERVER['REQUEST_URI']);
        $type = explode('/',$x['path']);
        switch ($type[1]){
            case 'pos-sales-return': {
                $form_type = 'pos-sales-return';
                break;
            }
            case 'sale-return': {
                $form_type = 'sale-return';
                break;
            }
            case 'sale-return-noauth': {
                $form_type = 'sale-return';
                break;
            }
        }
        if(!isset($form_type)){
            abort('404');
        }
        DB::beginTransaction();
        try{

            if(isset($id)){
                if($form_type == 'pos-sales-return'){
                    $saleReturn = TblSaleSales::where('sales_id',$id)->where('sales_type','RPOS')->where($currentBCB)->first();
                    $sales_type = 'RPOS';
                }
                if($form_type == 'sale-return'){
                    $saleReturn = TblSaleSales::where('sales_id',$id)->where('sales_type','SR')->where($currentBCB)->first();
                    $sales_type = 'SR';

                }
            }else{
                $saleReturn = new TblSaleSales();
                $saleReturn->sales_id = Utilities::uuid();
                $doc_data = [
                    'biz_type'          => 'branch',
                    'model'             => 'TblSaleSales',
                    'code_field'        => 'sales_code',
                    'code_prefix'       => strtoupper('sr'),
                    'code_type_field'   => 'sales_type',
                    'code_type'         => strtoupper('sr'),
                ];
                $saleReturn->sales_code = Utilities::documentCode($doc_data);
                $sales_type = 'SR';
            }
            $form_id = $saleReturn->sales_id;
            $saleReturn->sales_type = $sales_type;
            $saleReturn->currency_id = $request->currency_id;
            $saleReturn->sales_date = date('Y-m-d', strtotime($request->sales_date));
            $saleReturn->payment_term_id = $request->payment_term_id;
            $saleReturn->sales_credit_days = $request->sales_credit_days;
            $saleReturn->customer_id = $request->customer_id;
            $saleReturn->sales_order_booking_id = $request->sales_order_booking_id;
            $saleReturn->sales_delivery_id = $request->sales_delivery_id;
            $saleReturn->sales_sales_man = $request->sales_sales_man;
            $saleReturn->sales_sales_type = $request->sales_sales_type;
            $saleReturn->payment_mode_id = $request->payment_mode_id;
            $saleReturn->sales_exchange_rate = $request->exchange_rate;
            //$saleReturn->sales_address = $request->sales_address;
            $saleReturn->sales_remarks = $request->sales_remarks;
            $saleReturn->sales_entry_status = 1;
            $saleReturn->business_id = 1;
            $saleReturn->company_id = 1;
            $saleReturn->branch_id = $branch_branch_id;
            $saleReturn->sales_user_id = 81;
            $saleReturn->sales_rate_type = $request->rate_type;
            $saleReturn->sales_rate_perc = $request->rate_perc;
            $saleReturn->save();

            $del_Dtls = TblSaleSalesDtl::where('sales_id',$id)->where($currentBCB)->get();
            foreach ($del_Dtls as $del_Dtl){
                TblSaleSalesDtl::where('sales_dtl_id',$del_Dtl->sales_dtl_id)->where($currentBCB)->delete();
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
                    if(isset($id) && isset($pd['sales_dtl_id']) && $pd['sales_dtl_id'] != 'undefined'){
                        $dtl->sales_dtl_id = $pd['sales_dtl_id'];
                        $dtl->sales_id = $id;
                    }else{
                        $dtl->sales_dtl_id = Utilities::uuid();
                        $dtl->sales_id = $saleReturn->sales_id;
                    }
                    $dtl->sales_type = $sales_type;
                    $dtl->sr_no = $sr_no++;
                    $dtl->sales_dtl_barcode = $pd['pd_barcode'];
                    $dtl->product_id = $pd['product_id'];
                    $dtl->product_barcode_id = $pd['product_barcode_id'];
                    $dtl->uom_id = $pd['uom_id'];
                    $dtl->sales_dtl_packing = $pd['pd_packing'];
                    $dtl->qty_base_unit = (isset($pd['pd_packing'])?$pd['pd_packing']:'0') * ((isset($pd['quantity'])?$pd['quantity']:'0')+(isset($pd['foc_qty'])?$pd['foc_qty']:'0'));
                    $dtl->sales_dtl_quantity = $this->addNo($pd['quantity']);
                    $dtl->sales_dtl_foc_qty = $this->addNo($pd['foc_qty']);
                    $dtl->sales_dtl_fc_rate = $this->addNo($pd['fc_rate']);
                    $dtl->sales_dtl_rate = $this->addNo($pd['rate']);
                    $dtl->sales_dtl_amount = $this->addNo($pd['amount']);
                    $dtl->sales_dtl_disc_per = $this->addNo($pd['dis_perc']);
                    $dtl->sales_dtl_disc_amount = $this->addNo($pd['dis_amount']);
                    $dtl->sales_dtl_vat_per = $this->addNo($pd['vat_perc']);
                    $dtl->sales_dtl_vat_amount = $this->addNo($pd['vat_amount']);
                    $dtl->sales_dtl_total_amount = $this->addNo($pd['gross_amount']);
                    $dtl->business_id = 1;
                    $dtl->company_id = 1;
                    $dtl->branch_id = $branch_branch_id;
                    $dtl->sales_dtl_user_id = 81;
                    $dtl->save();
                    $net_total += $this->addNo($pd['gross_amount']);
                    $total_net_amount += $this->addNo($pd['gross_amount']);
                    $amount_total += $this->addNo($pd['amount']);
                    $vat_amount_total += $this->addNo(isset($pd['vat_amount'])?$pd['vat_amount']:'0.00');
                    $disc_amount_total += $this->addNo(isset($pd['dis_amount'])?$pd['dis_amount']:'0.00');
                }
            }

            if(isset($id)){
                $del_Dtls = TblSaleSalesExpense::where('sales_id',$id)->where($currentBCB)->get();
                foreach ($del_Dtls as $del_Dtls){
                    TblSaleSalesExpense::where('sales_expense_id',$del_Dtls->sales_expense_id)->where($currentBCB)->delete();
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
                            $expenseDtl->sales_id = $saleReturn->sales_id;
                        }
                        $expenseDtl->chart_account_id = $expense['account_id'];
                        $expenseDtl->sales_expense_account_code = $expense['account_code'];
                        $expenseDtl->sales_expense_account_name = $expense['account_name'];
                        $expenseDtl->sales_expense_amount = $this->addNo($expense['expense_amount']);
                        $expenseDtl->business_id = 1;
                        $expenseDtl->company_id = 1;
                        $expenseDtl->branch_id = $branch_branch_id;
                        $expenseDtl->sales_expense_user_id = 81;
                        $expenseDtl->save();
                        $net_total += $this->addNo($expense['expense_amount']);
                        $TotalExpAmount += $this->addNo($expense['expense_amount']);
                    }
                }
            }

            $saleTotal = TblSaleSales::where('sales_id',$saleReturn->sales_id)->where($currentBCB)->first();
            $saleTotal->total_expense = abs($TotalExpAmount);
            $saleTotal->sales_net_amount = abs($total_net_amount) - abs($TotalExpAmount);
            $saleTotal->save();
            // insert update sale voucher
            $table_name = 'tbl_acco_voucher';
            if(isset($id)){
                $action = 'update';
                $saleReturn_id = $id;
                $saleReturn = TblSaleSales::where('sales_id',$saleReturn_id)->where($currentBCB)->first();
                $voucher_id = $saleReturn->voucher_id;
                if(!empty($saleReturn->voucher_id)){
                    $voucher_id = $saleReturn->voucher_id;
                }else{
                    $voucher_id = Utilities::uuid();
                }
            }else{
                $action = 'add';
                $saleReturn_id = $saleReturn->sales_id;
                $voucher_id = Utilities::uuid();
            }
            $where_clause = '';
            $customer = ViewSaleCustomer::where('customer_id',$request->customer_id)->where($currentBC)->first();


            if($request->sales_sales_type == 1){
                $customer_chart_account_id = $sessionSaleReturnCashAc;
            }
            if($request->sales_sales_type == 2){
                $customer_chart_account_id = (int)$customer->customer_account_id;
            }
            if($request->sales_sales_type == 3 || $request->sales_sales_type == 4 || $request->sales_sales_type == 5){
                $customer_chart_account_id = $saleReturn->bank_id;
                if($saleReturn->bank_id == 0){
                    return $this->jsonErrorResponse([], 'Bank Id Does not exist and entry will not update', 200);
                }
            }

            $sale_return_discount_ac = $sessionSaleReturnDiscount;
            $sale_return_income_ac = $sessionSaleReturnIncome;
            $sale_return_vat_payable_ac = $sessionSaleReturnVatPayable;
            $sale_return_cash_ac = $sessionSaleReturnCashAc;

            //check account code
            $ChartArr = [
                $customer_chart_account_id,
                $sale_return_discount_ac,
                $sale_return_income_ac,
                $sale_return_vat_payable_ac
            ];
            $response = $this->ValidateCharCode($ChartArr);
            if($response == false){
                return $this->returnjsonerror("voucher Account Code not correct",404);
            }

            //voucher start
            $data = [
                'voucher_id'            =>  $voucher_id,
                'voucher_document_id'   =>  $saleReturn_id,
                'voucher_no'            =>  $saleReturn->sales_code,
                'voucher_date'          =>  date('Y-m-d', strtotime($request->sales_date)),
                'voucher_descrip'       =>  'Sale Return: '.$saleReturn->sales_remarks,
                'voucher_type'          =>  $sales_type,
                'branch_id'             =>  $branch_branch_id,
                'business_id'           =>  1,
                'company_id'            =>  1,
                'voucher_user_id'       =>  81,
                'document_ref_account'  =>  (int)$customer->customer_account_id,
                'vat_amount'            =>  abs($vat_amount_total),
            ];
            $data['chart_account_id'] = $customer_chart_account_id;
            $data['voucher_debit'] = 0;
            $data['voucher_credit'] = abs($net_total);
            $data['voucher_sr_no'] = 1;
            // for debit entry net_total
            if($sales_type == 'RPOS'){
                $this->proAccoVoucherInsert($saleReturn_id,$action,$table_name,$data,$where_clause);
            }else{
                $this->proAccoVoucherInsert($voucher_id,$action,$table_name,$data,$where_clause);
            }


            $action = 'add';
            $data['chart_account_id'] = $sale_return_discount_ac;
            $data['voucher_debit'] = 0;
            $data['voucher_credit'] = abs($disc_amount_total);
            $data['voucher_sr_no'] = 2;
            // for debit entry disc_amount_total
            $this->proAccoVoucherInsert($voucher_id,$action,$table_name,$data,$where_clause);

            $action = 'add';
            $data['chart_account_id'] = $sale_return_income_ac;
            $data['voucher_debit'] = (abs($net_total) - abs($disc_amount_total));
            $data['voucher_credit'] = 0;
            $data['voucher_sr_no'] = 3;
            // for credit entry amount_total
            $this->proAccoVoucherInsert($voucher_id,$action,$table_name,$data,$where_clause);

            $action = 'add';
            $data['chart_account_id'] = $sale_return_income_ac;
            $data['voucher_debit'] = 0;
            $data['voucher_credit'] = abs($vat_amount_total);
            $data['voucher_sr_no'] = 4;
            // for credit entry amount_total
            $this->proAccoVoucherInsert($voucher_id,$action,$table_name,$data,$where_clause);

            $action = 'add';
            $data['chart_account_id'] = $sale_return_vat_payable_ac;
            $data['voucher_debit'] = abs($vat_amount_total);
            $data['voucher_credit'] = 0;
            $data['voucher_sr_no'] = 5;
            // for credit entry vat_amount_total
            $this->proAccoVoucherInsert($voucher_id,$action,$table_name,$data,$where_clause);

            if(isset($request->pdsm)){
                $sr_no = 5;
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
                    $this->proAccoVoucherInsert($voucher_id,$action,$table_name,$data,$where_clause);
                    $sr_no++;
                }
            }
            
            $sale = TblSaleSales::where('sales_id',$saleReturn_id)->where($currentBCB)->first();
            $sale->voucher_id = $voucher_id;
            $sale->save();
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
            $data['redirect'] = $this->prefixIndexPage.$form_type;
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

    public function print($id)
    {
        $x = parse_url($_SERVER['REQUEST_URI']);
        $type = explode('/',$x['path']);
        switch ($type[1]){
            case 'pos-sales-return': {
                $frm = 'POS Sales Return';
                $form_type = 'RPOS';
                break;
            }
            case 'sale-return': {
                $frm = 'Sale Return';
                $form_type = 'SR';
                break;
            }
        }
        if(!isset($frm)){
            abort('404');
        }

        $data['title'] = $frm;
        $data['permission'] = self::$menu_dtl_id.'-print';
        if(isset($id)){
            if(TblSaleSales::where('sales_id','LIKE',$id)->where(Utilities::currentBCB())->exists()){
                $data['current'] = TblSaleSales::with('dtls','customer','expense','SO')->where('sales_id',$id)->where('sales_type',$form_type)->where(Utilities::currentBCB())->first();
            }else{
                abort('404');
            }
        }
        $data['users'] = User::where('user_type','erp')->where('user_entry_status',1)->where(Utilities::currentBC())->where('id',$data['current']->sales_sales_man)->first();
        $data['payment_terms'] = TblAccoPaymentTerm::where('payment_term_id',$data['current']->payment_term_id)->where('payment_term_entry_status',1)->where(Utilities::currentBCB())->first();
        $data['currency'] = TblDefiCurrency::where('currency_id',$data['current']->currency_id)->where(Utilities::currentBCB())->first();
        $data['payment_type'] = TblDefiPaymentType::where('payment_type_entry_status',1)->where('payment_type_id',$data['current']->sales_sales_type)->where(Utilities::currentBCB())->first();

        return view('prints.sale_invoice_print',compact('data'));

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

            $saleReturn = TblSaleSales::where('sales_id',$id)->where('sales_type','SR')->where(Utilities::currentBCB())->first();
            $voucher_id = $saleReturn->voucher_id;
            if(!empty($voucher_id)){
                $this->proAccoVoucherDelete($voucher_id);
            }
            $saleReturn->dtls()->delete();
            $saleReturn->expense()->delete();
            $saleReturn->delete();

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
