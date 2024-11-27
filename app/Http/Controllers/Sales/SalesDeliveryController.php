<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use App\Library\Utilities;
use App\Models\TblAccoPaymentTerm;
use App\Models\TblDefiCurrency;
use App\Models\TblSaleCustomer;
use App\Models\TblSaleSalesDelivery;
use App\Models\TblSaleSalesDeliveryDtl;
use App\Models\TblSaleSalesDtl;
use App\Models\TblSoftPosInvoiceHeadings;
use App\Models\TblAccCoa;
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

class SalesDeliveryController extends Controller
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
    public static $page_title = 'Sales Delivery';
    public static $redirect_url = 'sales-delivery';
    public static $menu_dtl_id = '138';
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($id = null)
    {
        $data['page_data'] = [];
        $data['page_data']['path_index'] = $this->prefixIndexPage.self::$redirect_url;
        $data['page_data']['create'] = '/'.self::$redirect_url.$this->prefixCreatePage;
        if(isset($id)){
            if(TblSaleSalesDelivery::where('sales_delivery_id','LIKE',$id)->where(Utilities::currentBCB())->exists()){
                $data['permission'] = self::$menu_dtl_id.'-edit';
                $data['page_data'] = array_merge($data['page_data'], Utilities::editForm());
                $data['id'] = $id;
                $data['current'] = TblSaleSalesDelivery::with('dtls','customer','SO','sales_contract','sales_invoice')->where('sales_delivery_id',$id)->where(Utilities::currentBCB())->first();
                $data['document_code'] = $data['current']->sales_delivery_code;
                $data['page_data']['print'] = '/'.self::$redirect_url.'/print/'.$id;
            }
            else{
                abort('404');
            }
        }else{
            $data['permission'] = self::$menu_dtl_id.'-create';
            $data['page_data'] = array_merge($data['page_data'], Utilities::newForm());
            $data['document_code'] = $this->documentCode(TblSaleSalesDelivery::where(Utilities::currentBCB())->max('sales_delivery_code'),'SD');
            $data['customer'] = TblSaleCustomer::select(['customer_id','customer_name'])->where('customer_default_customer',1)->where(Utilities::currentBC())->first();
        }
        $data['users'] = User::where('user_type','erp')->where('user_entry_status',1)->where(Utilities::currentBC())->where('id',auth()->user()->id)->get();
        $data['payment_terms'] = TblAccoPaymentTerm::where(Utilities::currentBCB())->get();
        $data['currency'] = TblDefiCurrency::where('currency_entry_status',1)->where(Utilities::currentBC())->get();
        $data['accounts'] = TblAccCoa::where('chart_sale_expense_account',1)->where(Utilities::currentBC())->get();

        $data['payment_type'] = TblDefiPaymentType::where('payment_type_entry_status',1)
            ->where(Utilities::currentBCB())->get();
        $data['rate_types'] = config('constants.rate_type');

        $arr = [
            'biz_type' => 'business',
            'code' => $data['document_code'],
            'link' => $data['page_data']['create'],
            'table_name' => 'tbl_sale_sales_delivery',
            'col_id' => 'sales_delivery_id',
            'col_code' => 'sales_delivery_code',
        ];
        $data['switch_entry'] = $this->switchEntry($arr);
        return view('sales.sales_delivery.form',compact('data'));
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
            'customer_name' => 'required',
            'customer_id' => 'required|numeric',
            'currency_id' => 'required|numeric',
            'exchange_rate' => 'required|numeric',
            'payment_term_id' => 'nullable|numeric',
            'sales_credit_days' => 'nullable|numeric',
            'sales_sales_type' => 'required|numeric',
            'sales_sales_man' => 'nullable|numeric',
            'sales_remarks' => 'nullable|max:255',
            'pd.*.product_id' => 'nullable|numeric',
            'pd.*.product_barcode_id' => 'nullable|numeric',
        ]);
        if ($validator->fails()) {
            $data['validator_errors'] = $validator->errors();
            return $this->jsonErrorResponse($data, trans('message.required_fields'), 422);
        }
        if(isset($request->customer_id)){
            $exitsCustomer = TblSaleCustomer::where('customer_id',$request->customer_id)->where(Utilities::currentBC())->exists();
            if (!$exitsCustomer) {
                return $this->returnjsonerror("Customer Not Exist",201);
            }
        }
        if(!in_array($request->rate_type,array_flip(config('constants.rate_type')))){
            return $this->jsonErrorResponse($data, trans('message.required_fields'), 200);
        }
        /*if($request->pro_tot <= 0){
            return $this->returnjsonerror("Please Enter Product Detail",201);
        }*/
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
                    $sales = TblSaleSalesDelivery::where('sales_delivery_id',$id)->where(Utilities::currentBCB())->first();
                    $sales_type = 'SD';
            }else{
                $sales = new TblSaleSalesDelivery();
                $sales->sales_delivery_id = Utilities::uuid();
                $sales->sales_delivery_code = $this->documentCode(TblSaleSalesDelivery::where(Utilities::currentBCB())->max('sales_delivery_code'),'SD');
                $sales_type = 'SD';
            }
            $form_id = $sales->sales_delivery_id;
            $sales->sales_delivery_type = $sales_type;
            $sales->sales_id = $request->sales_id;
            $sales->currency_id = $request->currency_id;
            $sales->sales_delivery_date = date('Y-m-d', strtotime($request->sales_date));
            $sales->payment_term_id = $request->payment_term_id;
            $sales->sales_delivery_credit_days = $request->sales_credit_days;
            $sales->customer_id = $request->customer_id;
            $sales->sales_order_booking_id = $request->sales_order_booking_id;
            $sales->sales_delivery_no = $request->sales_delivery_no;
            $sales->sales_delivery_sales_man = $request->sales_sales_man;
            $sales->sales_delivery_sales_type = $request->sales_sales_type;
            $sales->payment_mode_id = $request->payment_mode_id;
            $sales->sales_delivery_exchange_rate = $request->exchange_rate;
            $sales->sales_delivery_mobile_no = $request->sales_mobile_no;
            //$sales->sales_address = $request->sales_address;
            $sales->sales_delivery_remarks = $request->sales_remarks;
            $sales->sales_delivery_net_amount = $request->Total_Amount;
            $sales->sales_delivery_entry_status = 1;
            $sales->business_id = auth()->user()->business_id;
            $sales->company_id = auth()->user()->company_id;
            $sales->branch_id = auth()->user()->branch_id;
            $sales->sales_delivery_user_id = auth()->user()->id;
            $sales->cashreceived = $request->receive_amount;
            $sales->change = (number_format($request->receive_amount,3) - number_format($request->pro_tot,3));
            $sales->sales_contract_id = $request->sales_contract_id;
            $sales->sales_delivery_rate_type = $request->rate_type;
            $sales->sales_delivery_rate_perc = $request->rate_perc;
            $sales->save();

            $del_Dtls = TblSaleSalesDeliveryDtl::where('sales_delivery_id',$id)->where(Utilities::currentBCB())->get();
            foreach ($del_Dtls as $del_Dtl){
                TblSaleSalesDeliveryDtl::where('sales_delivery_dtl_id',$del_Dtl->sales_delivery_dtl_id)->where(Utilities::currentBCB())->delete();
            }
            $sr_no = 1;
            $net_total = 0;
            $amount_total = 0;
            $vat_amount_total = 0;
            $disc_amount_total = 0;
            if(isset($request->pd)){
                foreach($request->pd as $pd){
                    $dtl = new TblSaleSalesDeliveryDtl();
                    if(isset($id) && isset($pd['sales_delivery_dtl_id']) && $pd['sales_delivery_dtl_id'] != 'undefined' ){
                        $dtl->sales_delivery_dtl_id = $pd['sales_delivery_dtl_id'];
                        $dtl->sales_delivery_id = $id;
                    }else{
                        $dtl->sales_delivery_dtl_id = Utilities::uuid();
                        $dtl->sales_delivery_id = $sales->sales_delivery_id;
                    }
                    $dtl->sr_no = $sr_no++;
                    $dtl->sales_delivery_type = $sales_type;
                    $dtl->sales_delivery_dtl_barcode = $pd['pd_barcode'];
                    $dtl->product_id = $pd['product_id'];
                    $dtl->uom_id = $pd['uom_id'];
                    $dtl->product_barcode_id = $pd['product_barcode_id'];
                    $dtl->sales_delivery_dtl_packing = $pd['pd_packing'];
                    $dtl->qty_base_unit = (isset($pd['pd_packing'])?$pd['pd_packing']:'0') * ((isset($pd['quantity'])?$pd['quantity']:'0')+(isset($pd['foc_qty'])?$pd['foc_qty']:'0'));
                    $dtl->sales_delivery_dtl_quantity = $this->addNo(isset($pd['quantity'])?$pd['quantity']:'0');
                    $dtl->sales_delivery_dtl_foc_qty = $this->addNo(isset($pd['foc_qty'])?$pd['foc_qty']:'0');
                    $dtl->sales_delivery_dtl_fc_rate = $this->addNo(isset($pd['fc_rate'])?$pd['fc_rate']:'0.00');
                    $dtl->sales_delivery_dtl_rate = $this->addNo(isset($pd['rate'])?$pd['rate']:'0.00');
                    $dtl->sales_delivery_dtl_amount = $this->addNo(isset($pd['amount'])?$pd['amount']:'0.00');
                    $dtl->sales_delivery_dtl_disc_per = $this->addNo(isset($pd['dis_perc'])?$pd['dis_perc']:'0.00');
                    $dtl->sales_delivery_dtl_disc_amount = $this->addNo(isset($pd['dis_amount'])?$pd['dis_amount']:'0.00');
                    $dtl->sales_delivery_dtl_vat_per = $this->addNo(isset($pd['vat_perc'])?$pd['vat_perc']:'0.00');
                    $dtl->sales_delivery_dtl_vat_amount = $this->addNo(isset($pd['vat_amount'])?$pd['vat_amount']:'0.00');
                    $dtl->sales_delivery_dtl_total_amount = $this->addNo(isset($pd['gross_amount'])?$pd['gross_amount']:'0.00');
                    $dtl->sales_delivery_dtl_gross_rate = $this->addNo(isset($pd['g_rate'])?$pd['g_rate']:'0.00');
                    $dtl->business_id = auth()->user()->business_id;
                    $dtl->company_id = auth()->user()->company_id;
                    $dtl->branch_id = auth()->user()->branch_id;
                    $dtl->sales_delivery_dtl_user_id = auth()->user()->id;
                    $dtl->save();

                    $net_total += $this->addNo($pd['gross_amount']);
                    $amount_total += $this->addNo($pd['amount']);
                    $vat_amount_total += $this->addNo(isset($pd['vat_amount'])?$pd['vat_amount']:'0.00');
                    $disc_amount_total += $this->addNo(isset($pd['dis_amount'])?$pd['dis_amount']:'0.00');
                }
            }
            //dd($dtl->toArray());
            /*if(isset($id)){
                $del_Dtls = TblSaleSalesExpense::where('sales_id',$id)->get();
                foreach ($del_Dtls as $del_Dtls){
                    TblSaleSalesExpense::where('sales_expense_id',$del_Dtls->sales_expense_id)->delete();
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
                    }
                }
            }
            */
            // insert update sale voucher
            $table_name = 'tbl_acco_voucher';
            if(isset($id)){
                $action = 'update';
                $sale_id = $id;
                $sale = TblSaleSalesDelivery::where('sales_delivery_id',$sale_id)->first();
                $voucher_id = $sale->voucher_id;

            }else{
                $action = 'add';
                $sale_id = $sales->sales_delivery_id;
                $voucher_id = Utilities::uuid();
            }
            $where_clause = '';
            $customer = TblSaleCustomer::where('customer_id',$request->customer_id)->where(Utilities::currentBC())->first();
            $customer_chart_account_id = (int)$customer->customer_account_id;

            //check account code
            $ChartArr = [
                $customer_chart_account_id,
                Session::get('dataSession')->sale_discount,
                Session::get('dataSession')->sale_income,
                Session::get('dataSession')->sale_vat_payable
            ];
            $response = $this->ValidateCharCode($ChartArr);
            if($response == false){
                return $this->returnjsonerror("voucher Account Code not correct",404);
            }

            //voucher start
            $data = [
                'voucher_id'            =>  $voucher_id,
                'voucher_document_id'   =>  $sale_id,
                'voucher_no'            =>  $sales->sales_delivery_code,
                'voucher_date'          =>  date('Y-m-d', strtotime($request->sales_date)),
                'voucher_descrip'       =>  'Sale: '.$sales->sales_delivery_remarks,
                'voucher_type'          =>  $sales_type,
                'branch_id'             =>  auth()->user()->branch_id,
                'business_id'           =>  auth()->user()->business_id,
                'company_id'            =>  auth()->user()->company_id,
                'voucher_user_id'       =>  auth()->user()->id
            ];
            $data['chart_account_id'] = $customer_chart_account_id;
            $data['voucher_debit'] = abs($net_total);
            $data['voucher_credit'] = 0;
            $data['voucher_sr_no'] = 1;
            // for debit entry net_total
            $this->proAccoVoucherInsert($voucher_id,$action,$table_name,$data,$where_clause);

            $action = 'add';
            $discount_chart_account_id = Session::get('dataSession')->sale_discount;
            $data['chart_account_id'] = $discount_chart_account_id;
            $data['voucher_debit'] = abs($disc_amount_total);
            $data['voucher_credit'] = 0;
            $data['voucher_sr_no'] = 2;
            // for debit entry disc_amount_total
            $this->proAccoVoucherInsert($voucher_id,$action,$table_name,$data,$where_clause);

            $action = 'add';
            $income_chart_account_id = Session::get('dataSession')->sale_income;
            $data['chart_account_id'] = $income_chart_account_id;
            $data['voucher_debit'] = 0;
            $data['voucher_credit'] = abs($amount_total);
            $data['voucher_sr_no'] = 3;
            // for credit entry amount_total
            $this->proAccoVoucherInsert($voucher_id,$action,$table_name,$data,$where_clause);

            $action = 'add';
            $vat_payable_chart_account_id = Session::get('dataSession')->sale_vat_payable;
            $data['chart_account_id'] = $vat_payable_chart_account_id;
            $data['voucher_debit'] = 0;
            $data['voucher_credit'] = abs($vat_amount_total);
            $data['voucher_sr_no'] = 4;
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

            if(!isset($id)){
                $sale = TblSaleSalesDelivery::where('sales_delivery_id',$sale_id)->where(Utilities::currentBCB())->first();
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
            $data['redirect'] = $this->prefixIndexPage.self::$redirect_url;;
            return $this->jsonSuccessResponse($data, trans('message.update'), 200);

        }else{
            $data = array_merge($data, Utilities::returnJsonNewForm());
            $data['redirect'] = '/'.self::$redirect_url.$this->prefixCreatePage.'/'.$form_id;
            //$data['print_url'] = route( 'prints.sale_delivery_print.blade',$sales->sales_delivery_id);
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

    public function SIDtl($id){

         $data = [];
        DB::beginTransaction();
        try {

            $data['all'] = TblSaleSalesDtl::with('product','barcode','uom','packing')->where('sales_id',$id)->where(Utilities::currentBCB())->get();

        } catch (QueryException $e) {
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
            return $this->jsonSuccessResponse($data, 'Data loaded', 200);
        }else{
            return $this->jsonErrorResponse($data, 'Something will be wrong', 200);
        }
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
        $data['title'] = 'Sales Delivery';
        $data['permission'] = self::$menu_dtl_id.'-print';
        if(isset($id)){
            if(TblSaleSalesDelivery::where('sales_delivery_id','LIKE',$id)->where(Utilities::currentBCB())->exists()){
                $data['current'] = TblSaleSalesDelivery::with('dtls','customer','SO','sales_contract','sales_invoice')->where('sales_delivery_id',$id)->where(Utilities::currentBCB())->first();
            }else{
                abort('404');
            }
        }
        $data['users'] = User::where('user_type','erp')->where('user_entry_status',1)->where(Utilities::currentBC())->where('id',$data['current']->sales_delivery_sales_man)->first();
        $data['payment_terms'] = TblAccoPaymentTerm::where('payment_term_id',$data['current']->payment_term_id)->where('payment_term_entry_status',1)->where(Utilities::currentBC())->first();
        $data['currency'] = TblDefiCurrency::where('currency_id',$data['current']->currency_id)->where(Utilities::currentBC())->first();
        $data['payment_type'] = TblDefiPaymentType::where('payment_type_entry_status',1)->where('payment_type_id',$data['current']->sales_delivery_sales_type)->where(Utilities::currentBC())->first();

        $data['invoice_headings'] = TblSoftPosInvoiceHeadings::pluck('heading_arabic_name','heading_key');

        return view('prints.sale_delivery_print',compact('data'));
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

            $sales = TblSaleSalesDelivery::where('sales_delivery_id',$id)->where(Utilities::currentBCB())->first();
            $voucher_id = $sales->voucher_id;
            if(!empty($voucher_id)){
                $this->proAccoVoucherDelete($voucher_id);
            }

            $sales->dtls()->delete();
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
