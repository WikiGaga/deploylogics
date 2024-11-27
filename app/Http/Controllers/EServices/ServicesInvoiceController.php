<?php

namespace App\Http\Controllers\EServices;

use Session;
use Exception;
use Validator;
use Dompdf\Dompdf;
use App\Models\User;
use ArPHP\I18N\Arabic;
use App\Models\TblAccCoa;
use App\Library\Utilities;
use App\Models\TblDefiArea;
use App\Models\TblDefiCity;
use App\Models\TblDefiStore;
use App\Models\TblSaleSales;
use Illuminate\Http\Request;
use App\Models\TblDefiCurrency;
use App\Models\TblSaleCustomer;
use App\Models\TblSaleSalesDtl;
use App\Models\ViewSaleCustomer;
use App\Models\TblSaleSalesOrder;
use App\Models\TblAccoPaymentTerm;
use App\Models\TblDefiOrderStatus;
use App\Models\TblDefiPaymentType;
use Illuminate\Support\Facades\DB;
use App\Models\TblSaleSalesExpense;
use App\Models\TblServUpdateStatus;
use App\Http\Controllers\Controller;
use Illuminate\Database\QueryException;
use App\Models\TblSoftPosInvoiceHeadings;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ServicesInvoiceController extends Controller
{
    public static $page_title = 'Services Invoice';
    public static $redirect_url = 'sales-invoice-c';
    public static $menu_dtl_id = '213';
    public static $type = 'SIC';

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
    public function create($id = null , $orderId = null)
    {
        $data['page_data']['title'] = 'Services Invoice';
        $data['form_type'] = 'sales-invoice-c';
        $data['invoice_menu_id'] = '213';
        $data['page_data']['create'] = '/'.$data['form_type'].$this->prefixCreatePage;
        if(isset($id) && !isset($orderId)){
            if(TblSaleSales::where('sales_id','LIKE',$id)->where(Utilities::currentBCB())->exists()){
                $data['permission'] = $data['invoice_menu_id'].'-edit';
                $data['page_data'] = array_merge($data['page_data'], Utilities::editForm());
                $data['id'] = $id;
                $qry = TblSaleSales::with('dtls','customer_view','expense','SO')->where(Utilities::currentBCB())->where('sales_id',$id);
                $data['current'] = $qry->first();
                $data['document_code'] = $data['current']->sales_code;
                $data['page_data']['print'] = '/'.$data['form_type'].'/print/'.$id;
                $data['users'] = User::where('user_type','erp')->where('user_entry_status',1)->where(Utilities::currentBC())->get();
            }
            else{
                abort('404');
            }
        }elseif(isset($id) && isset($orderId)){
            if(TblSaleSalesOrder::where('sales_order_id','LIKE',$orderId)->where(Utilities::currentBCB())->exists()){
                if(TblSaleSales::where('sales_order_booking_id',$orderId)->where(Utilities::currentBCB())->exists()){
                    $data['permission'] = $data['invoice_menu_id'].'-edit';
                    $data['page_data'] = array_merge($data['page_data'], Utilities::editForm());
                    $data['id'] = $id;
                    $qry = TblSaleSales::with('dtls','customer_view','expense','SO')->where(Utilities::currentBCB())->where('sales_order_booking_id',$orderId);
                    $data['current'] = $qry->first();
                    $data['document_code'] = $data['current']->sales_code;
                    
                    $data['page_data']['print'] = '/'.$data['form_type'].'/print/'. $data['current']->sales_id;
                    $data['users'] = User::where('user_type','erp')->where('user_entry_status',1)->where(Utilities::currentBC())->get();
                }else{
                    $data['permission'] = self::$menu_dtl_id.'-create';
                    $data['page_data'] = array_merge($data['page_data'], Utilities::newForm());
                    $data['current'] = TblSaleSalesOrder::with('dtls','expense','customer','sales_contract','sale_booking')->where(Utilities::currentBCB())->where('sales_order_id',$orderId)->first();
                    $data['areas'] = TblDefiArea::where('city_id' , $data['current']->city_id)->where('area_entry_status' , 1)->get();
                    
                    $data['document_code'] = $this->documentCode(TblSaleSales::where(Utilities::currentBCB())->where('sales_type','SIC')->max('sales_code'),'SIC');

                    $data['customer'] = TblSaleCustomer::select(['customer_id','customer_name'])->where(Utilities::currentBC())->where('customer_default_customer',1)->first();
                    
                    if($data['form_type'] == 'pos-sales-invoice'){
                        $data['users'] = User::where('user_type','erp')->where('user_entry_status',1)->where(Utilities::currentBC())->get();
                    }else{
                        $data['users'] = User::where('user_type','erp')->where('user_entry_status',1)->where(Utilities::currentBC())->where('id',auth()->user()->id)->get();
                    }
                }
            }else{
                abort('404');
            }
        }else{
            $data['permission'] = $data['invoice_menu_id'].'-create';
            $data['page_data'] = array_merge($data['page_data'], Utilities::newForm());
            $data['document_code'] = $this->documentCode(TblSaleSales::where(Utilities::currentBCB())->where('sales_type','SIC')->max('sales_code'),'SIC');
            $data['customer'] = TblSaleCustomer::select(['customer_id','customer_name'])->where('customer_default_customer',1)->where(Utilities::currentBC())->first();

            if($data['form_type'] == 'pos-sales-invoice'){
                $data['users'] = User::where('user_type','erp')->where('user_entry_status',1)->where(Utilities::currentBC())->get();
            }else{
                $data['users'] = User::where('user_type','erp')->where('user_entry_status',1)->where(Utilities::currentBC())->where('id',auth()->user()->id)->get();
            }
        }
        $data['payment_terms'] = TblAccoPaymentTerm::where(Utilities::currentBC())->get();
        $data['currency'] = TblDefiCurrency::where(Utilities::currentBC())->where('currency_entry_status',1)->get();
        $data['accounts'] = TblAccCoa::where(Utilities::currentBC())->where('chart_sale_expense_account',1)->get();
        $data['payment_type'] = TblDefiPaymentType::where(Utilities::currentBC())->where('payment_type_entry_status',1)->get();
        $data['rate_types'] = config('constants.rate_type');
        $data['bank_acc'] = TblAccCoa::where(Utilities::currentBC())->where('parent_account_id',66)->get(['chart_account_id','chart_name','chart_code']);
        $data['store'] = TblDefiStore::where('store_entry_status',1)->where(Utilities::currentBCB())->get();
        $data['cities'] = TblDefiCity::where('city_entry_status',1)->get();
        $data['order_status'] = TblDefiOrderStatus::where('order_status_entry_status' , 1)->get();
        
        $arr = [
            'biz_type' => 'business',
            'code' => $data['document_code'],
            'link' => $data['page_data']['create'],
            'table_name' => 'tbl_sale_sales',
            'col_id' => 'sales_id',
            'col_code' => 'sales_code',
            'code_type_field'   => 'sales_type',
            'code_type'         => strtoupper('sic'),
        ];
        $data['switch_entry'] = $this->switchEntry($arr);

        return view('e_services.services_invoice.form',compact('data'));
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
        if(in_array($request->sales_sales_type, [3,4,5]) && empty($request->bank_acc_id)) {
            return $this->jsonErrorResponse($data, "Bank account is required", 200);
        }
        if ($validator->fails()) {
            $data['validator_errors'] = $validator->errors();
            return $this->jsonErrorResponse($data, trans('message.required_fields'), 422);
        }
        if(isset($request->customer_id)){
            $exitsCustomer = ViewSaleCustomer::where('customer_id',$request->customer_id)->where(Utilities::currentBC())->exists();
            if (!$exitsCustomer) {
                return $this->returnjsonerror("Customer Not Exist",201);
            }
        }
        if(!in_array($request->rate_type,array_flip(config('constants.rate_type')))){
            return $this->jsonErrorResponse($data, trans('message.required_fields'), 200);
        }
        if($request->pro_tot <= 0){
            return $this->returnjsonerror("Please Enter Product Detail",201);
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
        
        $form_type = 'sales-invoice-c';

        DB::beginTransaction();
        try{
            if(isset($id)){
                $sales = TblSaleSales::where('sales_id',$id)->where('sales_type','SIC')->where(Utilities::currentBCB())->first();
                $sales_type = 'SIC';
            }else{
                $sales = new TblSaleSales();
                $sales->sales_id = Utilities::uuid();
                $sales->sales_code = $this->documentCode(TblSaleSales::where(Utilities::currentBCB())->where('sales_type','SIC')->max('sales_code'),'SIC');
                $sales_type = 'SIC';
            }
            $form_id = $sales->sales_id;
            $sales->sales_type = $sales_type;
            // $sales->store_id = $request->store_id;
            $sales->currency_id = $request->currency_id;
            $sales->sales_date = date('Y-m-d', strtotime($request->sales_date));
            $sales->payment_term_id = $request->payment_term_id;
            $sales->sales_credit_days = $request->sales_credit_days;
            $sales->customer_id = $request->customer_id;
            $sales->sales_order_booking_id = $request->services_order_id;
            $sales->sales_delivery_id = $request->sales_delivery_id;
            $sales->sales_sales_man = $request->sales_sales_man;
            $sales->sales_sales_type = $request->sales_sales_type;
            $sales->payment_mode_id = $request->payment_mode_id;
            $sales->sales_exchange_rate = $request->exchange_rate;
            $sales->sales_mobile_no = $request->sales_mobile_no;
            $sales->bank_id = $request->bank_acc_id;
            //$sales->sales_address = $request->sales_address;
            $sales->sales_remarks = $request->sales_remarks;
            $sales->sales_net_amount = $request->Total_Amount;
            $sales->sales_entry_status = 1;
            $sales->business_id = auth()->user()->business_id;
            $sales->company_id = auth()->user()->company_id;
            $sales->branch_id = auth()->user()->branch_id;
            $sales->sales_user_id = auth()->user()->id;
            /*$sales->cashreceived = $request->receive_amount;
            $sales->change = (number_format($request->receive_amount,3) - number_format($request->pro_tot,3));
            */
            $sales->sales_contract_id = $request->sales_contract_id;
            $sales->sales_rate_type = $request->rate_type;
            $sales->sales_rate_perc = $request->rate_perc;
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
                    $dtl->sales_type = $sales_type;
                    $dtl->sales_dtl_barcode = $pd['pd_barcode'];
                    $dtl->product_id = $pd['product_id'];
                    $dtl->uom_id = $pd['uom_id'];
                    $dtl->product_barcode_id = $pd['product_barcode_id'];
                    $dtl->sales_dtl_packing = $pd['pd_packing'];
                    $dtl->sales_dtl_length = $this->addNo(isset($pd['pd_length'])?$pd['pd_length']:'');
                    $dtl->sales_dtl_width = $this->addNo(isset($pd['pd_width'])?$pd['pd_width']:'');
                    $dtl->qty_base_unit = (isset($pd['pd_packing'])?$pd['pd_packing']:'0') * ((isset($pd['quantity'])?$pd['quantity']:'0')+(isset($pd['foc_qty'])?$pd['foc_qty']:'0'));
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
                    $dtl->sales_dtl_notes = isset($pd['pd_notes']) ? $pd['pd_notes'] : "";
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

            // Update the Order Status
            if(isset($request->order_status) && isset($request->services_order_id)){
                TblSaleSalesOrder::where('sales_order_id' , $request->services_order_id)->update(['sales_order_status'=>$request->order_status]);
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
            $customer = ViewSaleCustomer::where('customer_id',$request->customer_id)->first();
            
            if($request->sales_sales_type == 1){
                $customer_chart_account_id = (int)Session::get('dataSession')->sale_cash_ac;
            }
            if($request->sales_sales_type == 2){
                $customer_chart_account_id = (int)$customer->customer_account_id;
            }
            if($request->sales_sales_type == 3 || $request->sales_sales_type == 4 || $request->sales_sales_type == 5){
                $customer_chart_account_id = $sales->bank_id;
                if($sales->bank_id == 0){
                    return $this->jsonErrorResponse([], 'Bank Id Does not exist and entry will not update', 200);
                }
            }
            //check account code
            $ChartArr = [
                $customer_chart_account_id,
                Session::get('dataSession')->sale_discount,
                Session::get('dataSession')->sale_income,
                Session::get('dataSession')->sale_vat_payable
            ];
            $response = $this->ValidateCharCode($ChartArr);
            if($response == false){
                return $this->returnjsonerror("Voucher Account Code not correct",404);
            }
            //voucher start
            $data = [
                'voucher_id'            =>  $voucher_id,
                'voucher_document_id'   =>  $sale_id,
                'voucher_no'            =>  $sales->sales_code,
                'voucher_date'          =>  date('Y-m-d', strtotime($request->sales_date)),
                'voucher_descrip'       =>  $sales_type.': '.$sales->sales_remarks,
                'voucher_type'          =>  $sales_type,
                'branch_id'             =>  auth()->user()->branch_id,
                'business_id'           =>  auth()->user()->business_id,
                'company_id'            =>  auth()->user()->company_id,
                'voucher_user_id'       =>  auth()->user()->id,
                'document_ref_account'  =>  (int)$customer->customer_account_id,
                'vat_amount'            =>  $vat_amount_total,
            ];
            $data['chart_account_id'] = $customer_chart_account_id;
            $data['voucher_debit'] = abs($net_total);
            $data['voucher_credit'] = 0;
            $data['voucher_sr_no'] = 1;
            // for debit entry net_total

            if($sales_type == 'POS'){
                $this->proAccoVoucherInsert($sale_id,$action,$table_name,$data,$where_clause);
            }else{
                $this->proAccoVoucherInsert($voucher_id,$action,$table_name,$data,$where_clause);
            }

            $action = 'add';
            $discount_chart_account_id = Session::get('dataSession')->sale_discount;
            $data['chart_account_id'] = $discount_chart_account_id;
            $data['voucher_debit'] = abs($disc_amount_total);
            $data['voucher_credit'] = 0;
            $data['voucher_sr_no'] = 2;
            // for debit entry disc_amount_total
            $this->proAccoVoucherInsert($voucher_id,$action,$table_name,$data,$where_clause);
          //  dump($amount_total);
         //   dump($vat_amount_total);
            $action = 'add';
            $income_chart_account_id = Session::get('dataSession')->sale_income;
            $data['chart_account_id'] = $income_chart_account_id;
            $data['voucher_debit'] = 0;
            $data['voucher_credit'] = (abs($amount_total) + abs($vat_amount_total));
            $data['voucher_sr_no'] = 3;
            // for credit entry amount_total
            $this->proAccoVoucherInsert($voucher_id,$action,$table_name,$data,$where_clause);

            $action = 'add';
            $income_chart_account_id = Session::get('dataSession')->sale_income;
            $data['chart_account_id'] = $income_chart_account_id;
            $data['voucher_debit'] = abs($vat_amount_total);
            $data['voucher_credit'] = 0;
            $data['voucher_sr_no'] = 4;
            // for credit entry amount_total
            $this->proAccoVoucherInsert($voucher_id,$action,$table_name,$data,$where_clause);

            $action = 'add';
            $vat_payable_chart_account_id = Session::get('dataSession')->sale_vat_payable;
            $data['chart_account_id'] = $vat_payable_chart_account_id;
            $data['voucher_debit'] = 0;
            $data['voucher_credit'] = abs($vat_amount_total);
            $data['voucher_sr_no'] = 5;
            // for credit entry vat_amount_total
            $this->proAccoVoucherInsert($voucher_id,$action,$table_name,$data,$where_clause);

            if(isset($request->pdsm)){
                $sr_no = 6;
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
            $data['redirect'] = $this->prefixIndexPage.$form_type;
            return $this->jsonSuccessResponse($data, trans('message.update'), 200);

        }else{
            $data['redirect'] = '/'.self::$redirect_url.$this->prefixCreatePage.'/'.$form_id;
            $data = array_merge($data, Utilities::returnJsonNewForm());
            //$data['print_url'] = route( 'prints.sale_invoice_thermal_print.blade',['thermal',$sales->sales_id] );
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

    public function print($id = null , $type = null)
    {
        if(!isset($id)){
            abort('404');
        }

        $form_type = 'SIC';
        $data['title'] = 'Sale Invoice';
        $data['type'] = $type;
        $data['invoice_menu_id'] = '213';
        $data['print_link'] = '/'.self::$redirect_url.'/print/'.$id.'/pdf';
        $data['permission'] = $data['invoice_menu_id'].'-print';
        if(isset($id)){
            if(TblSaleSales::where('sales_id','LIKE',$id)->where(Utilities::currentBCB())->exists()){
                $data['current'] = TblSaleSales::with('dtls','customer','expense','SO')->where(Utilities::currentBCB())->where('sales_id',$id)->where('sales_type',$form_type)->first();
            }else{
                abort('404');
            }
        }
        $data['users'] = User::where('user_type','erp')->where('user_entry_status',1)->where(Utilities::currentBC())->where('id',$data['current']->sales_sales_man)->first();
        $data['payment_terms'] = TblAccoPaymentTerm::where('payment_term_id',$data['current']->payment_term_id)->where(Utilities::currentBC())->where('payment_term_entry_status',1)->first();
        $data['currency'] = TblDefiCurrency::where('currency_id',$data['current']->currency_id)->where(Utilities::currentBC())->first();
        $data['payment_type'] = TblDefiPaymentType::where('payment_type_entry_status',1)->where(Utilities::currentBC())->where('payment_type_id',$data['current']->sales_sales_type)->first();

        $data['invoice_headings'] = TblSoftPosInvoiceHeadings::pluck('heading_arabic_name','heading_key');

        if(isset($type) && $type=='pdf'){
            $view = view('prints.e_services.sale_invoice_print_pdf', compact('data'))->render();

            $Arabic = new Arabic();
            $p = $Arabic->arIdentify($view);
            for ($i = count($p)-1; $i >= 0; $i-=2) {
                $utf8ar = $Arabic->utf8Glyphs(substr($view, $p[$i-1], $p[$i] - $p[$i-1]));
                $view   = substr_replace($view, $utf8ar, $p[$i-1], $p[$i] - $p[$i-1]);
            }

            //dd($view);
            $dompdf = new Dompdf();
            $options = $dompdf->getOptions();
            $options->set('dpi', 100);
            $options->set('isPhpEnabled', TRUE);
            $options->set('isHtml5ParserEnabled', TRUE);
            $options->setDefaultFont('arial');
            $dompdf->setOptions($options);
            $dompdf->loadHtml($view,'UTF-8');
            // (Optional) Setup the paper size and orientation
            $paper_orientation = 'portrait';
            // $customPaper = array(25,0,272,1122);
            $customPaper = array(0,0,242,1122);
            $dompdf->setPaper($customPaper);
            // Render the HTML as PDF
            $dompdf->render();

            // Output the generated PDF to Browser
            return $dompdf->stream();
        }else{
            return view('prints.e_services.services_invoice_print_small',compact('data'));
        }
        
        // if($type == 'thermal'){
        //     return view('prints.e_services.services_invoive_thermal_print',compact('data'));
        // }
    }

    /**
     * filter orderdata to invoice data
     *
     * @param array $arr
     * @return void
     */
    public function filterData($arr = []){
        $data = [];

        $data['fc_rate'] = isset($arr->tbl_purc_grn_dtl_fc_rate) ? $arr->tbl_purc_grn_dtl_fc_rate : 0;
        $data['purc_rate'] = isset($arr->product_barcode_purchase_rate) ? $arr->product_barcode_purchase_rate : $arr->tbl_purc_grn_dtl_rate;
        $data['disc_per'] = isset($arr->tbl_purc_grn_dtl_disc_percent) ? $arr->tbl_purc_grn_dtl_disc_percent : 0;
        $data['vat_per'] = isset($arr->tbl_purc_grn_dtl_vat_percent) ? $arr->tbl_purc_grn_dtl_vat_percent : 0;
        $data['disc_amount'] = isset($arr->tbl_purc_grn_dtl_disc_amount) ? $arr->tbl_purc_grn_dtl_disc_amount : 0;
        $data['vat_amount'] = isset($arr->tbl_purc_grn_dtl_vat_amount) ? $arr->tbl_purc_grn_dtl_vat_amount : 0;

        return $data;
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

            $sales = TblSaleSales::where('sales_id',$id)->where(Utilities::currentBCB())->first();
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
