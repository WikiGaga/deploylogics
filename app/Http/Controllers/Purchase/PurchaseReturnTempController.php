<?php

namespace App\Http\Controllers\Purchase;
use App\Models\TblDefiCurrency;
use App\Models\TblPurcProductBarcode;
use App\Models\TblPurcPurchaseOrder;
use App\Models\TblPurcPurchaseOrderDtl;
use App\Models\TblPurcSupplier;
use App\Models\TblPurcGrn;
use App\Models\TblPurcGrnDtl;
use App\Models\TblDefiStore;
use App\Models\TblDefiPaymentType;
use App\Models\TblAccoPaymentTerm;
use App\Models\TblAccCoa;
use App\Models\TblPurcGrnExpense;
use App\Models\TblPurcProduct;
use Illuminate\Support\Facades\DB;
use App\Library\Utilities;
use Illuminate\Validation\Rule;
use Dompdf\Dompdf;
use Validator;
use Exception;
use Session;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PurchaseReturnTempController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        
    }
    public static $page_title = 'Purchase Return Temporary';
    public static $redirect_url = 'purchase-return-temp';
    public static $menu_dtl_id = '50';

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($id = null)
    {
        $data['page_data'] = [];
        $data['form_type'] = 'purc_return';
        $data['page_data']['title'] = self::$page_title;
        $data['page_data']['path_index'] = $this->prefixIndexPage.self::$redirect_url;
        $data['page_data']['create'] = '/'.self::$redirect_url.$this->prefixCreatePage;
        if(isset($id)){
            if(TblPurcGrn::where('grn_id',$id)->where(Utilities::currentBCB())->exists()){
                $data['page_data'] = array_merge($data['page_data'], Utilities::editForm());
                $data['permission'] = self::$menu_dtl_id.'-edit';
                $data['id'] = $id;
                $data['current'] = TblPurcGrn::with('grn_dtl','supplier','PO','grn_expense')->where('grn_id',$id)->where('grn_type','PRT')->where(Utilities::currentBCB())->first();
                $data['grn_code'] = $data['current']->grn_code;
                $data['page_data']['print'] = '/'.self::$redirect_url.'/print/'.$id;
            }else{
                abort('404');
            }
        }else{
            $data['permission'] = self::$menu_dtl_id.'-create';
            $data['page_data'] = array_merge($data['page_data'], Utilities::newForm());
            $doc_data = [
                'biz_type'          => 'branch',
                'model'             => 'TblPurcGrn',
                'code_field'        => 'grn_code',
                'code_prefix'       => strtoupper('prt'),
                'code_type_field'   => 'grn_type',
                'code_type'         => strtoupper('prt'),
            ];
            $data['grn_code'] = Utilities::documentCode($doc_data);
        }
        $data['currency'] = TblDefiCurrency::where(Utilities::currentBC())->get();
        $data['accounts'] = TblAccCoa::where('chart_purch_expense_account',1)->where(Utilities::currentBC())->get();
        $data['store'] = TblDefiStore::where('store_entry_status',1)->where(Utilities::currentBCB())->get();
        $data['payment_type'] = TblDefiPaymentType::where('payment_type_entry_status',1)->where(Utilities::currentBC())->get();
        $data['payment_terms'] = TblAccoPaymentTerm::where('payment_term_entry_status',1)->where(Utilities::currentBC())->get();
        $arr = [
            'biz_type' => 'branch',
            'code' => $data['grn_code'],
            'link' => $data['page_data']['create'],
            'table_name' => 'tbl_purc_grn',
            'col_id' => 'grn_id',
            'col_code' => 'grn_code',
            'code_type_field'   => 'grn_type',
            'code_type'         => strtoupper('prt'),
        ];
        $data['switch_entry'] = $this->switchEntry($arr);
        return view('purchase.purchase_return_temp.form', compact('data'));
    }
    public function getPO($id){
        $data['all'] = TblPurcPurchaseOrder::with('po_details','supplier','lpo')->where('purchase_order_id',$id)->where(Utilities::currentBCB())->first();
        return response()->json($data);
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
            'purchase_order_id' => 'nullable|numeric',
            'grn_currency' => 'required|numeric',
            'exchange_rate' => 'required|numeric',
            'grn_store' => 'required|numeric',
            'grn_ageing_term_id' => 'nullable|numeric',
            'grn_ageing_term_value' => 'nullable|numeric',
            'payment_type_id' => 'required|numeric',
            'grn_notes' => 'nullable|max:255',
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
                    $exits = TblAccCoa::where('chart_account_id',$expense['account_id'])->where('chart_code',$expense['account_code'])->exists();
                    if (!$exits) {
                        return $this->returnjsonerror(" Account Code not correct",201);
                    }
                }else{
                    return $this->returnjsonerror(" Enter Account Code",201);
                }
            }
        }
        if(isset($request->pd)){
            foreach($request->pd as $dtl){
                $purchase_order_id = isset($dtl['purchase_order_id'])?$dtl['purchase_order_id']:"";
                $product = $dtl['product_id'];
                $product_barcode = $dtl['product_barcode_id'];
                $uom_id = $dtl['uom_id'];
                if($purchase_order_id != ""){
                    $exist_barcode = false;
                    $purchase_order_barcodes = TblPurcPurchaseOrderDtl::where('purchase_order_id',$purchase_order_id)->where(Utilities::currentBCB())->get();
                    foreach ($purchase_order_barcodes as $barcode){
                        if($barcode['product_id'] == $product && $barcode['uom_id'] == $uom_id && $barcode['product_barcode_id'] == $product_barcode){
                            $exist_barcode = true;
                        }
                    }
                    if($exist_barcode == false){
                        return $this->jsonErrorResponse($data, trans('message.not_barcode'), 422);
                    }
                    $purchase_order_id = "";
                }else{
                    if(TblPurcProduct::where('product_id','LIKE',$product)->where(Utilities::currentBC())->exists()){
                        $product_data = TblPurcProduct::with('product_barcode')->where('product_id',$product)->where(Utilities::currentBC())->first();
                        if(count($product_data->product_barcode) != 0){
                            $exist_barcode = false;
                            foreach ($product_data->product_barcode as $barcode){
                                if($product_barcode == $barcode['product_barcode_id']){
                                    if($uom_id == $barcode['uom']['uom_id']){
                                        $exist_barcode = true;
                                    }
                                }
                            }
                            if($exist_barcode == false){
                                return $this->jsonErrorResponse($data, trans('message.not_barcode'), 422);
                            }
                        }else{
                            return $this->jsonErrorResponse($data, trans('message.not_barcode'), 422);
                        }
                    }else{
                        return $this->jsonErrorResponse($data, trans('message.not_product'), 422);
                    }
                }
            }
        }else{
            return $this->returnjsonerror(" Enter Product Detail",201);
        }
        DB::beginTransaction();
        try{
            if(isset($id)){
                $pr = TblPurcGrn::where('grn_type','PRT')->where('grn_id',$id)->where(Utilities::currentBCB())->first();
            }else{
                $pr = new TblPurcGrn();
                $pr->grn_id = Utilities::uuid();
                $doc_data = [
                    'biz_type'          => 'branch',
                    'model'             => 'TblPurcGrn',
                    'code_field'        => 'grn_code',
                    'code_prefix'       => strtoupper('prt'),
                    'code_type_field'   => 'grn_type',
                    'code_type'         => strtoupper('prt'),
                ];
                $pr->grn_code = Utilities::documentCode($doc_data);
            }
            $form_id = $pr->grn_id;
            $pr->grn_type = 'PRT';
            $pr->grn_exchange_rate = $request->exchange_rate;
            $pr->payment_type_id = $request->payment_type_id;
            $pr->grn_date = date('Y-m-d', strtotime($request->grn_date));
            $pr->supplier_id = $request->supplier_id;
            $pr->purchase_order_id = $request->purchase_order_id;
            //$pr->grn_receiving_date = date('Y-m-d', strtotime($request->grn_receiving_date));
            $pr->store_id = $request->grn_store;
            $pr->grn_ageing_term_id = $request->grn_ageing_term_id;
            $pr->grn_ageing_term_value = $request->grn_ageing_term_value;
            $pr->grn_freight = $request->grn_freight;
            $pr->currency_id = $request->grn_currency;
            $pr->grn_bill_no = $request->grn_bill_no;
            $pr->grn_other_expense = $request->grn_other_expenses;
            $pr->grn_remarks = $request->grn_notes;
            $pr->business_id = auth()->user()->business_id;
            $pr->company_id = auth()->user()->company_id;
            $pr->branch_id = auth()->user()->branch_id;
            $pr->grn_user_id = auth()->user()->id;
            $pr->save();

            $pr_dtls = TblPurcGrnDtl::where('grn_id',$pr->grn_id)->where('grn_type','PRT')->where(Utilities::currentBCB())->get();
            foreach($pr_dtls as $pr_dtl){
                TblPurcGrnDtl::where('purc_grn_dtl_id',$pr_dtl->purc_grn_dtl_id)->where(Utilities::currentBCB())->delete();
            }
            $net_total = 0;
            $amount_total = 0;
            $vat_amount_total = 0;
            $disc_amount_total = 0;
            $TotalExpAmount = 0;
            $total_gross_amount = 0;
            if(isset($request->pd)){
                $sr_no = 1;
                foreach($request->pd as $dtl){
                    $prDtl = new TblPurcGrnDtl();
                    if(isset($id) && isset($account['purc_grn_dtl_id'])){
                        $prDtl->grn_id = $id;
                        $prDtl->purc_grn_dtl_id = $account['purc_grn_dtl_id'];
                    }else{
                        $prDtl->purc_grn_dtl_id = Utilities::uuid();
                        $prDtl->grn_id  = $pr->grn_id;
                    }
                    $prDtl->grn_type = 'PRT';
                    $prDtl->sr_no = $sr_no;
                    $sr_no = $sr_no+1;
                    $prDtl->purchase_order_id = isset($dtl['purchase_order_id'])?$dtl['purchase_order_id']:"";
                    $prDtl->supplier_id = $dtl['grn_supplier_id'];
                    $prDtl->product_id = $dtl['product_id'];
                    $prDtl->product_barcode_id = $dtl['product_barcode_id'];
                    $prDtl->uom_id  = $dtl['uom_id'];
                    $prDtl->tbl_purc_grn_dtl_packing = $dtl['pd_packing'];
                    $prDtl->qty_base_unit = (isset($dtl['pd_packing'])?$dtl['pd_packing']:'0') * ((isset($dtl['quantity'])?$dtl['quantity']:'0')+(isset($dtl['foc_qty'])?$dtl['foc_qty']:'0'));
                    $prDtl->tbl_purc_grn_dtl_supplier_barcode = $dtl['grn_supplier_barcode'];
                    $prDtl->product_barcode_barcode = $dtl['pd_barcode'];
                    $prDtl->tbl_purc_grn_dtl_quantity = $dtl['quantity'];
                    $prDtl->tbl_purc_grn_dtl_foc_quantity = $dtl['foc_qty'];
                    $prDtl->tbl_purc_grn_dtl_fc_rate = $this->addNo($dtl['fc_rate']);
                    $prDtl->tbl_purc_grn_dtl_rate = $this->addNo($dtl['rate']);
                    $prDtl->tbl_purc_grn_dtl_amount = $this->addNo($dtl['amount']);
                    $prDtl->tbl_purc_grn_dtl_disc_percent = $this->addNo($dtl['dis_perc']);
                    $prDtl->tbl_purc_grn_dtl_disc_amount = $this->addNo($dtl['dis_amount']);
                    $prDtl->tbl_purc_grn_dtl_gst_percent = '';//$this->addNo($dtl['grn_gst']);
                    $prDtl->tbl_purc_grn_dtl_vat_percent = $this->addNo($dtl['vat_perc']);
                    $prDtl->tbl_purc_grn_dtl_vat_amount = $this->addNo($dtl['vat_amount']);
                    $prDtl->tbl_purc_grn_dtl_batch_no = $dtl['batch_no'];
                    $prDtl->tbl_purc_grn_dtl_production_date = date('Y-m-d', strtotime($dtl['production_date']));
                    $prDtl->tbl_purc_grn_dtl_expiry_date = date('Y-m-d', strtotime($dtl['expiry_date']));
                    $prDtl->tbl_purc_grn_dtl_total_amount = $this->addNo($dtl['gross_amount']);
                    $prDtl->business_id = auth()->user()->business_id;
                    $prDtl->company_id = auth()->user()->company_id;
                    $prDtl->branch_id = auth()->user()->branch_id;
                    $prDtl->tbl_purc_grn_dtl_user_id = auth()->user()->id;

                    $barcode = TblPurcProductBarcode::where('product_barcode_id',$dtl['product_barcode_id'])
                        ->where('product_id',$dtl['product_id'])->first();

                    if($dtl['foc_qty'] > 0){
                        $amount = $this->addNo($dtl['amount']);
                        $quantity = $this->addNo($dtl['quantity']);
                        $foc_qty = $this->addNo($dtl['foc_qty']);
                        $barcode_packing = $barcode->product_barcode_packing;
                        $rate_inc_foc = ((float)$amount / ((float) $quantity + (float) $foc_qty )) / (float)$barcode_packing;
                    }else{
                        $rate = $this->addNo($dtl['rate']);
                        $rate_inc_foc = (float)$rate/(float)$barcode->product_barcode_packing;
                    }
                    $prDtl->tbl_purc_grn_dtl_rate_inc_foc = $rate_inc_foc;


                    $prDtl->save();
                    $net_total += $this->addNo($dtl['gross_amount']);
                    $total_gross_amount += $this->addNo($dtl['gross_amount']);
                    $amount_total += $this->addNo($dtl['amount']);
                    $vat_amount_total += $this->addNo($dtl['vat_amount']);
                    $disc_amount_total += $this->addNo($dtl['dis_amount']);
                }
            }
            if(isset($id)){
                $del_Dtls = TblPurcGrnExpense::where('grn_id',$id)->where(Utilities::currentBCB())->get();
                foreach ($del_Dtls as $del_Dtls){
                    TblPurcGrnExpense::where('grn_expense_id',$del_Dtls->grn_expense_id)->where(Utilities::currentBCB())->delete();
                }
            }
            if(isset($request->pdsm)){
                foreach($request->pdsm as $expense){
                    if(isset($expense['expense_amount'])){
                        $expenseDtl = new TblPurcGrnExpense();
                        $expenseDtl->grn_expense_id = Utilities::uuid();
                        if(isset($id)){
                            $expenseDtl->grn_id = $id;
                        }else{
                            $expenseDtl->grn_id = $pr->grn_id;
                        }
                        $expenseDtl->chart_account_id = $expense['account_id'];
                        $expenseDtl->grn_expense_account_code = $expense['account_code'];
                        $expenseDtl->grn_expense_account_name = $expense['account_name'];
                        $expenseDtl->grn_expense_amount = $this->addNo($expense['expense_amount']);
                        $expenseDtl->business_id = auth()->user()->business_id;
                        $expenseDtl->company_id = auth()->user()->company_id;
                        $expenseDtl->branch_id = auth()->user()->branch_id;
                        $expenseDtl->grn_expense_user_id = auth()->user()->id;
                        $expenseDtl->save();
                        $net_total += $this->addNo($expense['expense_amount']);
                        $TotalExpAmount += $this->addNo($expense['expense_amount']);
                    }
                }
            }

            $prTotal = TblPurcGrn::where('grn_id',$pr->grn_id)->where('grn_type','PRT')->where(Utilities::currentBCB())->first();
            $prTotal->grn_total_amount = $total_gross_amount;
            $prTotal->grn_total_expense_amount = $TotalExpAmount;
            $prTotal->grn_total_net_amount = $total_gross_amount - $TotalExpAmount;
            $prTotal->save();
           
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
        $data['title'] = 'Purchase Return Temporary';
        $data['type'] = $type;
        $data['permission'] = self::$menu_dtl_id.'-print';
        $data['print_link'] = '/purchase-return-temp/print/'.$id.'/pdf';
        if(isset($id)){
            if(TblPurcGrn::where('grn_id',$id)->where(Utilities::currentBCB())->exists()){
                $data['current'] = TblPurcGrn::with('grn_dtl','supplier','PO','grn_expense')->where('grn_id',$id)->where('grn_type','PRT')->where(Utilities::currentBCB())->first();
            }else{
                abort('404');
            }
        }
        $data['currency'] = TblDefiCurrency::where('currency_id',$data['current']->currency_id)->where(Utilities::currentBC())->first();
        $data['store'] = TblDefiStore::where('store_id',$data['current']->store_id)->where(Utilities::currentBCB())->first();
        $data['payment_terms'] = TblAccoPaymentTerm::where('payment_term_id',$data['current']->grn_ageing_term_id)->where('payment_term_entry_status',1)->where(Utilities::currentBC())->first();

        if(isset($type) && $type=='pdf'){
            $view = view('prints.purchase_return_temp_print', compact('data'))->render();
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
            return view('prints.purchase_return_temp_print', compact('data'));
        }
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

            $pr = TblPurcGrn::where('grn_id',$id)->where('grn_type','PRT')->where(Utilities::currentBCB())->first();
            $voucher_id = $pr->voucher_id;
            if(!empty($voucher_id)){
                $this->proAccoVoucherDelete($voucher_id);
            }
            $pr->grn_dtl()->delete();
            $pr->delete();

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
