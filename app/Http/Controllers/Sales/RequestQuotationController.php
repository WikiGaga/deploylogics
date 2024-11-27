<?php

namespace App\Http\Controllers\Sales;

use Exception;
use Validator;
use Dompdf\Dompdf;
use App\Models\User;
use App\Models\TblAccCoa;
use App\Library\Utilities;
use App\Models\TblDefiArea;
use App\Models\TblDefiCity;
use App\Models\TblDefiStore;
use App\Models\TblSaleSales;
use Illuminate\Http\Request;
use App\Models\TblDefiCurrency;
use App\Models\TblSaleCustomer;
use Illuminate\Validation\Rule;
// db and Validator
use App\Models\TblSaleSalesOrder;
use App\Models\TblAccoPaymentTerm;
use App\Models\TblDefiPaymentType;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\TblSaleSalesOrderDtl;
use Illuminate\Database\QueryException;
use App\Models\TblSaleSalesOrderExpense;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Validator as FacadesValidator;

class RequestQuotationController extends Controller
{
    public static $page_title = 'Request Quotation';
    public static $redirect_url = 'request-quotation';
    public static $menu_dtl_id = '212';
    public static $type = 'rq';


    public function create($id = null)
    {
        $data['page_data'] = [];
        $data['page_data']['title'] = self::$page_title;
        $data['page_data']['path_index'] = $this->prefixIndexPage.self::$redirect_url;
        $data['page_data']['create'] = '/'.self::$redirect_url.$this->prefixCreatePage;
        if(isset($id)){
            if(TblSaleSalesOrder::where('sales_order_id','LIKE',$id)->where(Utilities::currentBCB())->exists()){
                $data['permission'] = self::$menu_dtl_id.'-edit';
                $data['page_data'] = array_merge($data['page_data'], Utilities::editForm());
                $data['id'] = $id;
                $data['current'] = TblSaleSalesOrder::with('dtls','expense','customer','sales_contract','sale_booking')->where(Utilities::currentBCB())->where('sales_order_id',$id)->first();
                $data['areas'] = TblDefiArea::where('city_id' , $data['current']->city_id)->where('area_entry_status' , 1)->get();
                $data['document_code'] = $data['current']->sales_order_code;
                $data['page_data']['print'] = '/'.self::$redirect_url.'/print/'.$id;            
            }else{
                abort('404');
            }
        }else{
            $data['permission'] = self::$menu_dtl_id.'-create';
            $data['page_data'] = array_merge($data['page_data'], Utilities::newForm());
            $doc_data = [
                'biz_type'          => 'branch',
                'model'             => 'TblSaleSalesOrder',
                'code_field'        => 'sales_order_code',
                'code_prefix'       => strtoupper(self::$type),
                'code_type_field'   => 'sales_order_code_type',
                'code_type'         => self::$type
            ];
            $data['document_code'] = Utilities::documentCode($doc_data);
            $data['customer'] = TblSaleCustomer::select(['customer_id','customer_name'])->where(Utilities::currentBC())->where('customer_default_customer',1)->first();
        }
        $data['users'] = User::where('user_type','erp')->where('user_entry_status',1)->where(Utilities::currentBC())->where('id',auth()->user()->id)->get();
        $data['payment_terms'] = TblAccoPaymentTerm::where(Utilities::currentBC())->get();
        $data['cities'] = TblDefiCity::where('city_entry_status',1)->get();
        $data['currency'] = TblDefiCurrency::where('currency_entry_status',1)->where(Utilities::currentBC())->get();
        $data['accounts'] = TblAccCoa::where('chart_sale_expense_account',1)->where(Utilities::currentBC())->get();
        $data['store'] = TblDefiStore::where('store_entry_status',1)->where(Utilities::currentBCB())->get();
        $data['rate_types'] = config('constants.rate_type');
        $data['bank_acc'] = TblAccCoa::where(Utilities::currentBC())->where('parent_account_id',66)->get(['chart_account_id','chart_name','chart_code']);

        $data['payment_type'] = TblDefiPaymentType::where('payment_type_entry_status',1)
            ->where(Utilities::currentBC())->get();
        $arr = [
            'biz_type' => 'branch',
            'code' => $data['document_code'],
            'link' => $data['page_data']['create'],
            'table_name' => 'tbl_sale_sales_order',
            'col_id' => 'sales_order_id',
            'col_code' => 'sales_order_code',
            'code_type_field'   => 'sales_order_code_type',
            'code_type'         => self::$type,
        ];
        $data['switch_entry'] = $this->switchEntry($arr);
        return view('sales.request_quotation.form',compact('data'));
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
            'sales_order_credit_days' => 'nullable|numeric',
            'sales_order_sales_type' => 'required|numeric',
            'sales_order_sales_man' => 'required|numeric',
            'sales_order_remarks' => 'nullable|max:255',
            'pd.*.product_id' => 'nullable|numeric',
            'pd.*.product_barcode_id' => 'nullable|numeric',
        ]);
        if ($validator->fails()) {
            $data['validator_errors'] = $validator->errors();
            return $this->jsonErrorResponse($data, trans('message.required_fields'), 422);
        }
        if(isset($request->pdsm)){
            foreach($request->pdsm as $expense){
                if(!empty($expense['account_code'])){
                    $exits = TblAccCoa::where('chart_account_id',$expense['account_id'])->where('chart_code',$expense['account_code'])->where(Utilities::currentBC())->exists();
                    if (!$exits) {
                        return $this->returnjsonerror("Account Code not correct",201);
                    }
                }else{
                    return $this->returnjsonerror("Enter Account Code",201);
                }
            }
        }
        DB::beginTransaction();
        try{
            if(isset($id)){
                $sales_order = TblSaleSalesOrder::where('sales_order_id',$id)->where(Utilities::currentBCB())->first();
            }else{
                $sales_order = new TblSaleSalesOrder();
                $sales_order->sales_order_id = Utilities::uuid();
                $doc_data = [
                    'biz_type'          => 'branch',
                    'model'             => 'TblSaleSalesOrder',
                    'code_field'        => 'sales_order_code',
                    'code_prefix'       => strtoupper(self::$type),
                    'code_type_field'   => 'sales_order_code_type',
                    'code_type'         => self::$type
                ];
                $sales_order->sales_order_code = Utilities::documentCode($doc_data);
            }
            $form_id = $sales_order->sales_order_id;
            $sales_order->currency_id = $request->currency_id;
            $sales_order->sales_order_exchange_rate = $request->exchange_rate;
            $sales_order->sales_order_date = date('Y-m-d', strtotime($request->sales_order_date));;
            $sales_order->payment_term_id = $request->payment_term_id;
            $sales_order->sales_order_credit_days = $request->sales_order_credit_days;
            $sales_order->customer_id = $request->customer_id;
            $sales_order->sales_order_sales_man = $request->sales_order_sales_man;
            $sales_order->sales_order_sales_type = $request->sales_order_sales_type;
            $sales_order->payment_mode_id = $request->payment_mode_id;
            $sales_order->sales_order_address = $request->sales_order_address;
            $sales_order->sales_order_remarks = $request->sales_order_remarks;
            $sales_order->sales_order_entry_status = 1;

            $sales_order->sales_order_mobile_no = $request->sales_mobile_no;
            $sales_order->sales_order_rate_type = $request->rate_type;
            $sales_order->sales_order_rate_perc = $request->rate_perc;

            $sales_order->city_id = $request->sales_order_city_id;
            $sales_order->area_id = $request->sales_order_area_id;
            $sales_order->sub_total = $request->sub_total;
            $sales_order->net_total = $request->net_total;
            
            $sales_order->sales_order_status = 1;

            $sales_order->business_id = auth()->user()->business_id;
            $sales_order->company_id = auth()->user()->company_id;
            $sales_order->branch_id = auth()->user()->branch_id;
            $sales_order->sales_order_user_id = auth()->user()->id;
            $sales_order->sales_order_code_type = self::$type;
            $sales_order->save();

            $del_Dtls = TblSaleSalesOrderDtl::where('sales_order_id',$id)->where(Utilities::currentBCB())->get();
            foreach ($del_Dtls as $del_Dtl){
                TblSaleSalesOrderDtl::where('sales_order_dtl_id',$del_Dtl->sales_order_dtl_id)->where(Utilities::currentBCB())->delete();
            }
            if(isset($request->pd)){
                $sr_no = 1;
                foreach($request->pd as $pd){
                    $dtl = new TblSaleSalesOrderDtl();
                    if(isset($id) && isset($pd['sales_order_dtl_id']) && $pd['sales_order_dtl_id'] != 'undefined' ){
                        $dtl->sales_order_dtl_id = $pd['sales_order_dtl_id'];
                        $dtl->sales_order_id = $id;
                    }else{
                        $dtl->sales_order_dtl_id = Utilities::uuid();
                        $dtl->sales_order_id = $sales_order->sales_order_id;
                    }
                    $dtl->sr_no = $sr_no++;
                    $dtl->sales_order_dtl_barcode = $pd['pd_barcode'];
                    $dtl->product_id = $pd['product_id'];
                    $dtl->product_barcode_id = $pd['product_barcode_id'];
                    $dtl->uom_id = $pd['uom_id'];
                    $dtl->sales_order_dtl_notes = isset($pd['pd_notes']) ? $pd['pd_notes'] : "";
                    $dtl->sales_order_dtl_packing = $this->addNo(isset($pd['pd_packing'])?$pd['pd_packing']:0);
                    $dtl->sales_order_dtl_length = $this->addNo(isset($pd['pd_length'])?$pd['pd_length']:0);
                    $dtl->sales_order_dtl_width = $this->addNo(isset($pd['pd_width'])?$pd['pd_width']:0);
                    $dtl->sales_order_dtl_quantity = $this->addNo(isset($pd['quantity'])?$pd['quantity']:0);
                    $dtl->sales_order_dtl_foc_qty = $this->addNo(isset($pd['foc_qty'])?$pd['foc_qty']:0);
                    $dtl->sales_order_dtl_fc_rate = $this->addNo(isset($pd['fc_rate'])?$pd['fc_rate']:0);
                    $dtl->sales_order_dtl_rate = $this->addNo(isset($pd['rate'])?$pd['rate']:0);
                    $dtl->sales_order_dtl_amount = $this->addNo(isset($pd['amount'])?$pd['amount']:0);
                    $dtl->sales_order_dtl_disc_per = $this->addNo(isset($pd['dis_perc'])?$pd['dis_perc']:0);
                    $dtl->sales_order_dtl_disc_amount = $this->addNo(isset($pd['dis_amount'])?$pd['dis_amount']:0);
                    $dtl->sales_order_dtl_vat_per = $this->addNo(isset($pd['vat_perc'])?$pd['vat_perc']:0);
                    $dtl->sales_order_dtl_vat_amount = $this->addNo(isset($pd['vat_amount'])?$pd['vat_amount']:0);
                    $dtl->sales_order_dtl_total_amount = $this->addNo(isset($pd['gross_amount'])?$pd['gross_amount']:0);
                    $dtl->business_id = auth()->user()->business_id;
                    $dtl->company_id = auth()->user()->company_id;
                    $dtl->branch_id = auth()->user()->branch_id;
                    $dtl->sales_order_dtl_user_id = auth()->user()->id;
                    $dtl->save();
                }
            }

             if(isset($id)){
                 $del_AccDtls = TblSaleSalesOrderExpense::where('sales_order_id',$id)->where(Utilities::currentBCB())->get();
                 foreach ($del_AccDtls as $del_accDtl){
                    TblSaleSalesOrderExpense::where('sales_order_expense_id',$del_accDtl->sales_order_expense_id)->where(Utilities::currentBCB())->delete();
                 }
             }
            if(isset($request->pdsm)){
                foreach($request->pdsm as $expense){
                    $expenseDtl = new TblSaleSalesOrderExpense();
                    if(isset($id) && isset($account['sales_order_expense_id'])){
                        $expenseDtl->sales_order_id = $id;
                        $expenseDtl->sales_order_expense_id = $expense['sales_order_expense_id'];
                    }else{
                        $expenseDtl->sales_order_expense_id = Utilities::uuid();
                        $expenseDtl->sales_order_id = $sales_order->sales_order_id;
                    }
                    $expenseDtl->chart_account_id = $expense['account_id'];
                    $expenseDtl->sales_order_expense_account_code = $expense['account_code'];
                    $expenseDtl->sales_order_expense_account_name = $expense['account_name'];
                    $expenseDtl->sales_order_expense_amount = $this->addNo($expense['expense_amount']);
                    $expenseDtl->business_id = auth()->user()->business_id;
                    $expenseDtl->company_id = auth()->user()->company_id;
                    $expenseDtl->branch_id = auth()->user()->branch_id;
                    $expenseDtl->sales_order_expense_user_id = auth()->user()->id;
                    $expenseDtl->save();
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
    public function print($id,$type = null)
    {
        $data['title'] = 'Sales Quotation';
        $data['type'] = $type;
        $data['permission'] = self::$menu_dtl_id.'-print';
        $data['print_link'] = '/'.self::$redirect_url.'/print/'.$id.'/pdf';
        if(isset($id)){
            if(TblSaleSalesOrder::where('sales_order_id','LIKE',$id)->where(Utilities::currentBCB())->exists()){
                $data['permission'] = self::$menu_dtl_id.'-edit';
                $data['id'] = $id;
                $data['current'] = TblSaleSalesOrder::with('dtls','expense','customer','sales_contract','sale_booking')->where(Utilities::currentBCB())->where('sales_order_id',$id)->first();
                $data['document_code'] = $data['current']->sales_order_code;
                $data['page_data']['print'] = '/'.self::$redirect_url.'/print/'.$id;
            }else{
                abort('404');
            }
        }
        $data['users'] = User::where('id',$data['current']->sales_order_sales_man)->first();
        $data['payment_terms'] = TblAccoPaymentTerm::where('payment_term_id',$data['current']->payment_term_id)->where(Utilities::currentBC())->where('payment_term_entry_status',1)->first();
        $data['currency'] = TblDefiCurrency::where('currency_id',$data['current']->currency_id)->where(Utilities::currentBC())->first();

        $data['rate_types'] = config('constants.rate_type');
        if(isset($type) && $type=='pdf'){
            $view = view('prints.e_services.request_quotation_print', compact('data'))->render();
            //dd($view);
            $dompdf = new Dompdf();
            $options = $dompdf->getOptions();
            $options->set('dpi', 100);
            $options->set('isPhpEnabled', TRUE);
            $options->set('isHtml5ParserEnabled', TRUE);
            $options->setDefaultFont('roboto');
            $dompdf->setOptions($options);
            $dompdf->loadHtml($view,'UTF-8');
            // (Optional) Setup the paper size and orientation
            $dompdf->setPaper('A4', 'landscape');
            // Render the HTML as PDF
            $dompdf->render();

            // Output the generated PDF to Browser
            return $dompdf->stream();
        }else{
            return view('prints.e_services.request_quotation_print',compact('data'));
        }
    }

    function getSalesQutationDtlData(Request $request){

        $validator = Validator::make($request->all(), [
            'quotation_id' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            $data['validator_errors'] = $validator->errors();
            return $this->jsonErrorResponse($data, 'Please Select Qutation Code First', 422);
        }

        $id = $request->quotation_id;
        DB::beginTransaction();
        try{

            $data['quotation'] = TblSaleSalesOrder::with('dtls','expense','customer','sales_contract','sale_booking')->where(Utilities::currentBCB())->where('sales_order_id',$id)->first();

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
        return $this->jsonSuccessResponse($data, 'Sales Quotation Data loaded', 200);

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
            // return $this->jsonErrorResponse($data, trans('message.not_delete'), 200);
            $sales = TblSaleSales::where('sales_order_booking_id',$id)->where(Utilities::currentBCB())->first();
            if($sales == null)
            {
                $saleOrder = TblSaleSalesOrder::where('sales_order_id',$id)->where(Utilities::currentBCB())->first();
                $saleOrder->dtls()->delete();
                $saleOrder->expense()->delete();
                $saleOrder->delete();
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
