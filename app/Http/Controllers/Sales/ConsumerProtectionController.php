<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use App\Library\Utilities;
use App\Models\TblAccoPaymentTerm;
use App\Models\TblDefiCurrency;
use App\Models\TblPurcQuotationAccount;
use App\Models\TblSaleCustomer;
use App\Models\TblSaleConsumerProtection;
use App\Models\TblSaleConsumerProtectionDtl;
use App\Models\TblSaleConsumerProtectionExpense;
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

class ConsumerProtectionController extends Controller
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
    public static $page_title = 'Consumer Protection';
    public static $redirect_url = 'consumer-protection';
    public static $menu_dtl_id = '182';
    //public static $menu_dtl_id = '155';
    public static $type = 'cp';
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
            if(TblSaleConsumerProtection::where('protection_id','LIKE',$id)->where(Utilities::currentBCB())->exists()){
                $data['permission'] = self::$menu_dtl_id.'-edit';
                $data['page_data'] = array_merge($data['page_data'], Utilities::editForm());
                $data['id'] = $id;
                $data['current'] = TblSaleConsumerProtection::with('dtls','customer_view','expense','SO')->where(Utilities::currentBCB())->where('protection_id',$id)->first(); 
                $data['users'] = User::where('user_type','erp')->where('user_entry_status',1)->where(Utilities::currentBC())->get();
                $data['document_code'] = $data['current']->protection_code;
                $data['page_data']['print'] = '/'.self::$redirect_url.'/print/'.$id;
            }
            else{
                abort('404');
            }
        }else{
            $data['permission'] =  self::$menu_dtl_id.'-create';
            $data['page_data'] = array_merge($data['page_data'], Utilities::newForm());
            $data['document_code'] = $this->documentCode(TblSaleConsumerProtection::where(Utilities::currentBCB())->max('protection_code'),'CP');
            $data['customer'] = TblSaleCustomer::select(['customer_id','customer_name'])->where('customer_default_customer',1)->where(Utilities::currentBC())->first();
            $data['users'] = User::where('user_type','erp')->where('user_entry_status',1)->where(Utilities::currentBC())->where('id',auth()->user()->id)->get();
        }
        
        $data['payment_terms'] = TblAccoPaymentTerm::where(Utilities::currentBC())->get();
        $data['currency'] = TblDefiCurrency::where(Utilities::currentBC())->where('currency_entry_status',1)->get();
        $data['accounts'] = TblAccCoa::where(Utilities::currentBC())->where('chart_sale_expense_account',1)->get();
        $data['payment_type'] = TblDefiPaymentType::where(Utilities::currentBC())->where('payment_type_entry_status',1)->get();
        $data['rate_types'] = config('constants.rate_type');
        $arr = [
            'biz_type' => 'business',
            'code' => $data['document_code'],
            'link' => $data['page_data']['create'],
            'table_name' => 'tbl_sale_consumer_protection',
            'col_id' => 'protection_id',
            'col_code' => 'protection_code',
        ];
        $data['switch_entry'] = $this->switchEntry($arr);
        return view('sales.consumer_protection.form',compact('data'));
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
        DB::beginTransaction();
        try{
            if(isset($id)){
                $protection = TblSaleConsumerProtection::where('protection_id',$id)->where(Utilities::currentBCB())->first();
            }else{
                $protection = new TblSaleConsumerProtection();
                $protection->protection_id = Utilities::uuid();
                $protection->protection_code = $this->documentCode(TblSaleConsumerProtection::where(Utilities::currentBCB())->max('protection_code'),'CP');
            }
            $form_id = $protection->protection_id;
            $protection->protection_type = 'CP';
            $protection->currency_id = $request->currency_id;
            $protection->protection_date = date('Y-m-d', strtotime($request->sales_date));
            $protection->payment_term_id = $request->payment_term_id;
            $protection->protection_credit_days = $request->sales_credit_days;
            $protection->customer_id = $request->customer_id;
            $protection->sales_order_booking_id = $request->sales_order_booking_id;
            $protection->protection_delivery_id = $request->sales_delivery_id;
            $protection->protection_sales_man = $request->sales_sales_man;
            $protection->protection_sales_type = $request->sales_sales_type;
            $protection->payment_mode_id = $request->payment_mode_id;
            $protection->protection_exchange_rate = $request->exchange_rate;
            $protection->protection_mobile_no = $request->sales_mobile_no;
            //$sales->sales_address = $request->sales_address;
            $protection->protection_remarks = $request->sales_remarks;
            $protection->protection_net_amount = $request->Total_Amount;
            $protection->protection_entry_status = 1;
            $protection->business_id = auth()->user()->business_id;
            $protection->company_id = auth()->user()->company_id;
            $protection->branch_id = auth()->user()->branch_id;
            $protection->protection_user_id = auth()->user()->id;
            /*$sales->cashreceived = $request->receive_amount;
            $sales->change = (number_format($request->receive_amount,3) - number_format($request->pro_tot,3));
            */$protection->protection_contract_id = $request->sales_contract_id;
            $protection->protection_rate_type = $request->rate_type;
            $protection->protection_rate_perc = $request->rate_perc;
            $protection->save();

            $del_Dtls = TblSaleConsumerProtectionDtl::where('protection_id',$id)->where(Utilities::currentBCB())->get();
            foreach ($del_Dtls as $del_Dtl){
                TblSaleConsumerProtectionDtl::where('protection_dtl_id',$del_Dtl->protection_dtl_id)->where(Utilities::currentBCB())->delete();
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
                    $dtl = new TblSaleConsumerProtectionDtl();
                    if(isset($id) && isset($pd['protection_dtl_id']) && $pd['protection_dtl_id'] != 'undefined' ){
                        $dtl->protection_dtl_id = $pd['protection_dtl_id'];
                        $dtl->protection_id = $id;
                    }else{
                        $dtl->protection_dtl_id = Utilities::uuid();
                        $dtl->protection_id = $protection->protection_id;
                    }
                    $dtl->sr_no = $sr_no++;
                    $dtl->protection_type = 'CP';
                    $dtl->protection_dtl_barcode = $pd['pd_barcode'];
                    $dtl->product_id = $pd['product_id'];
                    $dtl->uom_id = $pd['uom_id'];
                    $dtl->product_barcode_id = $pd['product_barcode_id'];
                    $dtl->protection_dtl_packing = $pd['pd_packing'];
                    $dtl->qty_base_unit = (isset($pd['pd_packing'])?$pd['pd_packing']:'0') * ((isset($pd['quantity'])?$pd['quantity']:'0')+(isset($pd['foc_qty'])?$pd['foc_qty']:'0'));
                    $dtl->protection_dtl_quantity = $this->addNo(isset($pd['quantity'])?$pd['quantity']:'0');
                    $dtl->protection_dtl_foc_qty = $this->addNo(isset($pd['foc_qty'])?$pd['foc_qty']:'0');
                    $dtl->protection_dtl_fc_rate = $this->addNo(isset($pd['fc_rate'])?$pd['fc_rate']:'0.00');
                    $dtl->protection_dtl_rate = $this->addNo(isset($pd['rate'])?$pd['rate']:'0.00');
                    $dtl->protection_dtl_amount = $this->addNo(isset($pd['amount'])?$pd['amount']:'0.00');
                    $dtl->protection_dtl_disc_per = $this->addNo(isset($pd['dis_perc'])?$pd['dis_perc']:'0.00');
                    $dtl->protection_dtl_disc_amount = $this->addNo(isset($pd['dis_amount'])?$pd['dis_amount']:'0.00');
                    $dtl->protection_dtl_vat_per = $this->addNo(isset($pd['vat_perc'])?$pd['vat_perc']:'0.00');
                    $dtl->protection_dtl_vat_amount = $this->addNo(isset($pd['vat_amount'])?$pd['vat_amount']:'0.00');
                    $dtl->protection_dtl_total_amount = $this->addNo(isset($pd['gross_amount'])?$pd['gross_amount']:'0.00');
                    $dtl->protection_dtl_gross_rate = $this->addNo(isset($pd['g_rate'])?$pd['g_rate']:'0.00');
                    $dtl->business_id = auth()->user()->business_id;
                    $dtl->company_id = auth()->user()->company_id;
                    $dtl->branch_id = auth()->user()->branch_id;
                    $dtl->protection_dtl_user_id = auth()->user()->id;
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
                $del_Dtls = TblSaleConsumerProtectionExpense::where('protection_id',$id)->get();
                foreach ($del_Dtls as $del_Dtls){
                    TblSaleConsumerProtectionExpense::where(Utilities::currentBCB())->where('protection_expense_id',$del_Dtls->sales_expense_id)->delete();
                }
            }
            if(isset($request->pdsm)){
                foreach($request->pdsm as $expense){
                    if(isset($expense['expense_amount'])){
                        $expenseDtl = new TblSaleConsumerProtectionExpense();
                        $expenseDtl->protection_expense_id = Utilities::uuid();
                        if(isset($id)){
                            $expenseDtl->protection_id = $id;
                        }else{
                            $expenseDtl->protection_id = $protection->protection_id;
                        }
                        $expenseDtl->chart_account_id = $expense['account_id'];
                        $expenseDtl->protection_expense_account_code = $expense['account_code'];
                        $expenseDtl->protection_expense_account_name = $expense['account_name'];
                        $expenseDtl->protection_expense_amount = $this->addNo($expense['expense_amount']);
                        $expenseDtl->business_id = auth()->user()->business_id;
                        $expenseDtl->company_id = auth()->user()->company_id;
                        $expenseDtl->branch_id = auth()->user()->branch_id;
                        $expenseDtl->protection_expense_user_id = auth()->user()->id;
                        $expenseDtl->save();
                        $net_total += $this->addNo($expense['expense_amount']);
                        $TotalExpAmount += $this->addNo($expense['expense_amount']);
                    }
                }
            }

            $protectionTotal = TblSaleConsumerProtection::where('protection_id',$protection->protection_id)->where(Utilities::currentBCB())->first();
            $protectionTotal->total_expense = $TotalExpAmount;
            $protectionTotal->protection_net_amount = $total_net_amount - $TotalExpAmount;
            $protectionTotal->save();


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


    public function print($id)
    {
        
        $data['title'] = 'Consumer Protection';
        $data['permission'] =  self::$menu_dtl_id.'-print';
        if(isset($id)){
            if(TblSaleConsumerProtection::where('protection_id','LIKE',$id)->where(Utilities::currentBCB())->exists()){
                $data['current'] = TblSaleConsumerProtection::with('dtls','customer','expense','SO')->where(Utilities::currentBCB())->where('protection_id',$id)->first();
            }else{
                abort('404');
            }
        }
        $data['users'] = User::where('user_type','erp')->where('user_entry_status',1)->where(Utilities::currentBC())->where('id',$data['current']->protection_sales_man)->first();
        $data['payment_terms'] = TblAccoPaymentTerm::where('payment_term_id',$data['current']->payment_term_id)->where(Utilities::currentBC())->where('payment_term_entry_status',1)->first();
        $data['currency'] = TblDefiCurrency::where('currency_id',$data['current']->currency_id)->where(Utilities::currentBC())->first();
        $data['payment_type'] = TblDefiPaymentType::where('payment_type_entry_status',1)->where(Utilities::currentBC())->where('payment_type_id',$data['current']->protection_sales_type)->first();
        $data['invoice_headings'] = TblSoftPosInvoiceHeadings::pluck('heading_arabic_name','heading_key');
        return view('prints.consumer_protection_print',compact('data'));
        
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

            $protection = TblSaleConsumerProtection::where('protection_id',$id)->where(Utilities::currentBCB())->first();
            $protection->dtls()->delete();
            $protection->expense()->delete();
            $protection->delete();

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
