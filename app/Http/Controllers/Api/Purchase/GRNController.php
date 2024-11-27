<?php

namespace App\Http\Controllers\Api\Purchase;

use App\Http\Controllers\ApiController;
use App\Models\TblAccoVoucher;
use App\Models\TblDefiConfiguration;
use App\Models\TblDefiCurrency;
use App\Models\TblDefiPaymentType;
use App\Models\TblPurcProduct;
use App\Models\TblPurcProductBarcode;
use App\Models\TblPurcProductBarcodeDtl;
use App\Models\TblPurcProductBarcodePurchRate;
use App\Models\TblPurcPurchaseOrder;
use App\Models\TblPurcPurchaseOrderDtl;
use App\Models\TblPurcSupplier;
use App\Models\TblPurcGrn;
use App\Models\TblPurcGrnDtl;
use App\Models\TblPurcGrnExpense;
use App\Models\TblAccoPaymentTerm;
use App\Models\TblAccCoa;
use App\Models\TblDefiStore;
use App\Models\ViewPurcProductBarcodeHelp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Library\ApiUtilities;
use Illuminate\Validation\Rule;
use Validator;
use Exception;
use Session;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;



class GRNController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public static $page_title = 'GRN';
    public static $prefix = 'grn';

    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function create(Request $request){

    //    dd($request->toArray());
        $business_id = $request['business_id'];
        $branch_id = $request['branch_id'];
        $id = isset($request->id)?$request->id:"";

        $currentBC = [
            ['business_id', $business_id],
            ['company_id',$business_id]
        ];
        $currentBCB = [
            ['business_id', $business_id],
            ['company_id',$business_id],
            ['branch_id',$branch_id]
        ];

        $data['title'] = self::$page_title;
        if(!empty($id)){
            if(TblPurcGrn::where('grn_id',$id)->where($currentBCB)->exists()){
                $data['action'] = 'edit';
                $current = TblPurcGrn::with('grn_dtl','supplier','PO','grn_expense')->where('grn_id',$id)->where($currentBCB)->first();
                $data['current'] = $this->FilterData($current);
            }else{
                return $this->ApiJsonErrorResponse($data,'Not Found');
            }
        }else{
            $data['action'] = 'save';
            $doc_data = [
                    'biz_type'          => 'branch',
                    'model'             => 'TblPurcGrn',
                    'code_field'        => 'grn_code',
                    'code_prefix'       => strtoupper(self::$prefix),
                    'code_type_field'   => 'grn_type',
                    'code_type'         => strtoupper(self::$prefix),
                    'business_id'      => $business_id,
                    'branch_id'      => $branch_id,
                ];
            $data['grn_code'] = ApiUtilities::documentCode($doc_data);
        }

        $data['currency'] = TblDefiCurrency::select('currency_id','currency_name','currency_rate','currency_default')->where($currentBC)->get();

        $data['store'] = TblDefiStore::select('store_id','store_name','store_default_value')->where('store_entry_status',1)->where($currentBCB)->get();

        $data['payment_type'] = TblDefiPaymentType::select('payment_type_id','payment_type_name')->where('payment_type_entry_status',1)->where($currentBC)->get();
        $data['payment_type_id'] = 2;

        $data['payment_terms']  = TblAccoPaymentTerm::select('payment_term_id','payment_term_name')->where('payment_term_entry_status',1)->where($currentBC)->get();

        $data['accounts'] = TblAccCoa::select('chart_account_id','chart_code','chart_name')->where('chart_purch_expense_account',1)->where($currentBC)->get();


        if(count($data['currency']) == 0){$data['currency'] = (object)[];}
        if(count($data['store']) == 0){$data['store'] = (object)[];}
        if(count($data['payment_type']) == 0){$data['payment_type'] = (object)[];}
        if(count($data['payment_terms']) == 0){$data['payment_terms'] = (object)[];}
        if(count($data['accounts']) == 0){$data['accounts'] = (object)[];}

        return $this->ApiJsonSuccessResponse($data,'form data');
    }



    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $id = null)
    {
        $business_id = $request->business_id;
        $branch_id = $request->branch_id;
        $user_id = $request->user_id;
        $currentBC = [
            ['business_id', $business_id],
            ['company_id',$business_id]
        ];
        $currentBCB = [
            ['business_id', $business_id],
            ['company_id',$business_id],
            ['branch_id',$branch_id]
        ];
        //$id = isset($request->id)?$request->id:"";

        $data = [];
        $validator = Validator::make($request->all(), [
            'supplier_id' => 'required|numeric',
            'purchase_order_id' => 'nullable|numeric',
            'currency_id' => 'required|numeric',
            'exchange_rate' => 'nullable|numeric',
            'store_id' => 'required|numeric',
            'payment_term_id' => 'nullable|numeric',
            'payment_term_val' => 'nullable|numeric',
            'payment_type_id' => 'required|numeric',
            'notes' => 'nullable|max:255',
            'pd.*.product_id' => 'nullable|numeric',
            'pd.*.product_barcode_id' => 'nullable|numeric',
        ]);
        if ($validator->fails()) {
            return $this->ApiJsonErrorResponse($data,trans('message.required_fields'));
        }
        if(isset($request->pdsm)){
            foreach($request->pdsm as $expense){
                if(!empty($expense['account_code'])){
                    $exits = TblAccCoa::where('chart_account_id',$expense['account_id'])->where('chart_code',$expense['account_code'])->where($currentBC)->exists();
                    if (!$exits) {
                        return $this->ApiJsonErrorResponse($data,'Account Code not correct');
                    }
                }else{
                    return $this->ApiJsonErrorResponse($data,'Enter Account Code');
                }
            }
        }
        if(isset($request->pd)){
            foreach($request->pd as $dtl){
                $purchase_order_id = isset($dtl['purchase_order_id'])?$dtl['purchase_order_id']:"";
                $product = $dtl['product_id'];
                $product_barcode = $dtl['product_barcode_id'];
                $uom_id = $dtl['uom_id'];
                if(!empty($purchase_order_id)){
                    $exist_barcode = false;
                    $purchase_order_barcodes = TblPurcPurchaseOrderDtl::where('purchase_order_id',$purchase_order_id)->where($currentBCB)->get();
                    foreach ($purchase_order_barcodes as $barcode){
                        if($barcode['product_id'] == $product && $barcode['uom_id'] == $uom_id && $barcode['product_barcode_id'] == $product_barcode){
                            $exist_barcode = true;
                        }
                    }
                    if($exist_barcode == false){
                        return $this->ApiJsonErrorResponse($data,'Barcode Not Exist');
                    }
                    $purchase_order_id = "";
                }else{
                    if(!ViewPurcProductBarcodeHelp::where('product_barcode_id','LIKE',$product_barcode)->where($currentBC)->exists()){
                        return $this->ApiJsonErrorResponse($data,'Product Not Exist');
                    }
                }
            }
        }
        DB::beginTransaction();
        try{
            $sumOfProdTotalQty = 0;
            if(isset($request->pd)){
                foreach($request->pd as $dtl){
                    $prod_total_qty = (int)$dtl['quantity']+(int)$dtl['foc_qty'];
                    $sumOfProdTotalQty += $prod_total_qty;
                }
            }
            if(isset($id)){
                $grn = TblPurcGrn::where('grn_id',$id)->where($currentBCB)->first();
            }else{
                $grn = new TblPurcGrn();
                $grn->grn_id = ApiUtilities::uuid();

                $doc_data = [
                    'biz_type'          => 'branch',
                    'model'             => 'TblPurcGrn',
                    'code_field'        => 'grn_code',
                    'code_prefix'       => strtoupper(self::$prefix),
                    'code_type_field'   => 'grn_type',
                    'code_type'         => strtoupper(self::$prefix),
                    'business_id'      => $business_id,
                    'branch_id'      => $branch_id,
                ];
                $grn->grn_code = ApiUtilities::documentCode($doc_data);
            }
            $grn->grn_type = strtoupper(self::$prefix);
            $grn->grn_exchange_rate = $request->exchange_rate;
            $grn->payment_type_id = $request->payment_type_id;
            $grn->grn_date = date('Y-m-d', strtotime($request->grn_date));
            $grn->supplier_id = $request->supplier_id;
            $grn->purchase_order_id = $request->purchase_order_id;
            // $grn->grn_receiving_date = date('Y-m-d', strtotime($request->grn_receiving_date));
            $grn->store_id = $request->store_id;
            $grn->grn_ageing_term_id = $request->payment_term_id;
            $grn->grn_ageing_term_value = $request->payment_term_val;
            $grn->grn_freight = "";
            $grn->currency_id = $request->currency_id;
            $grn->grn_bill_no = $request->grn_bill_no;
            $grn->grn_other_expense = $request->total_expenses;
            $grn->grn_remarks = $request->notes;
            $grn->grn_overall_discount = "";
            $grn->grn_overall_disc_amount = "";
            $grn->business_id = $business_id;
            $grn->company_id = $business_id;
            $grn->branch_id = $branch_id;
            $grn->grn_user_id = $user_id;
            $grn->grn_device_id = 2;
            $grn->save();

            $net_total = 0;
            $amount_total = 0;
            $vat_amount_total = 0;
            $disc_amount_total = 0;
            $TotalExpAmount = 0;
            $total_gross_amount = 0;
            $TotalExpAmount += 0;
            if(isset($id)){
                $del_Dtls = TblPurcGrnExpense::where('grn_id',$id)->where($currentBCB)->get();
                foreach ($del_Dtls as $del_Dtls){
                    TblPurcGrnExpense::where('grn_expense_id',$del_Dtls->grn_expense_id)->where($currentBCB)->delete();
                }
            }
            if(isset($request->pdsm)){
                foreach($request->pdsm as $expense){
                    if(isset($expense['expense_amount'])){
                        $expenseDtl = new TblPurcGrnExpense();
                        $expenseDtl->grn_expense_id = ApiUtilities::uuid();
                        if(isset($id)){
                            $expenseDtl->grn_id = $id;
                        }else{
                            $expenseDtl->grn_id = $grn->grn_id;
                        }
                        $expenseDtl->chart_account_id = $expense['account_id'];
                        $expenseDtl->grn_expense_account_code = $expense['account_code'];
                        $expenseDtl->grn_expense_account_name = $expense['account_name'];
                        $expenseDtl->grn_expense_amount = $this->addNo($expense['expense_amount']);
                        $expenseDtl->business_id = $business_id;
                        $expenseDtl->company_id = $business_id;
                        $expenseDtl->branch_id = $branch_id;
                        $expenseDtl->grn_expense_user_id = $user_id;
                        $expenseDtl->save();
                        $net_total += $this->addNo($expense['expense_amount']);
                        $TotalExpAmount += $this->addNo($expense['expense_amount']);
                    }
                }
            }

            $grn_dtls = TblPurcGrnDtl::where('grn_id',$grn->grn_id)->where($currentBCB)->get();
            foreach($grn_dtls as $grn_dtl){
                TblPurcGrnDtl::where('purc_grn_dtl_id',$grn_dtl->purc_grn_dtl_id)->where($currentBCB)->delete();
            }
            if(isset($request->pd)){
                $sr_no = 1;
                foreach($request->pd as $dtl){
                    if($dtl['vat_perc'] > 0){
                        $updateVat = TblPurcProductBarcodeDtl::checkBarcodeVatPercStatus($dtl['product_barcode_id'],$dtl['vat_perc']);
                        if($updateVat == false){
                            return $this->jsonErrorResponse($data, $dtl['pd_barcode']. ": vat not updated", 200);
                        }
                    }
                    $grnDtl = new TblPurcGrnDtl();
                    if(isset($id) && isset($pd['purc_grn_dtl_id'])){
                        $grnDtl->grn_id = $id;
                        $grnDtl->purc_grn_dtl_id = $pd['purc_grn_dtl_id'];
                    }else{
                        $grnDtl->purc_grn_dtl_id = ApiUtilities::uuid();
                        $grnDtl->grn_id  = $grn->grn_id;
                    }
                    $grnDtl->grn_type = strtoupper(self::$prefix);
                    $grnDtl->sr_no = $sr_no;
                    $sr_no = $sr_no+1;
                    $grnDtl->purchase_order_id = isset($dtl['purchase_order_id'])?$dtl['purchase_order_id']:"";
                    $grnDtl->supplier_id = "";
                    $grnDtl->product_id = $dtl['product_id'];
                    $grnDtl->product_barcode_id = $dtl['product_barcode_id'];
                    $grnDtl->uom_id  = $dtl['uom_id'];
                    $grnDtl->tbl_purc_grn_dtl_packing = $dtl['pd_packing'];
                    $grnDtl->qty_base_unit = (isset($dtl['pd_packing'])?$dtl['pd_packing']:'0') * ((isset($dtl['quantity'])?$dtl['quantity']:'0')+(isset($dtl['foc_qty'])?$dtl['foc_qty']:'0'));
                    $grnDtl->tbl_purc_grn_dtl_supplier_barcode = "";
                    $grnDtl->product_barcode_barcode = $dtl['pd_barcode'];
                    $grnDtl->tbl_purc_grn_dtl_quantity = $dtl['quantity'];
                    $grnDtl->tbl_purc_grn_dtl_foc_quantity = $dtl['foc_qty'];
                    $grnDtl->tbl_purc_grn_dtl_fc_rate = $this->addNo($dtl['fc_rate']);
                    $grnDtl->tbl_purc_grn_dtl_rate = $this->addNo($dtl['rate']);
                    $grnDtl->tbl_purc_grn_dtl_amount = $this->addNo($dtl['amount']);
                    $grnDtl->tbl_purc_grn_dtl_disc_percent = $this->addNo($dtl['dis_perc']);
                    $grnDtl->tbl_purc_grn_dtl_disc_amount = $this->addNo($dtl['dis_amount']);
                    $grnDtl->tbl_purc_grn_dtl_gst_percent = ""; // $this->addNo($dtl['grn_gst']);
                    $grnDtl->tbl_purc_grn_dtl_vat_percent = $this->addNo($dtl['vat_perc']);
                    $grnDtl->tbl_purc_grn_dtl_vat_amount = $this->addNo($dtl['vat_amount']);
                    $grnDtl->tbl_purc_grn_dtl_batch_no = $dtl['batch_no'];
                    $grnDtl->tbl_purc_grn_dtl_production_date = date('Y-m-d', strtotime($dtl['production_date']));
                    $grnDtl->tbl_purc_grn_dtl_expiry_date = date('Y-m-d', strtotime($dtl['expiry_date']));
                    $grnDtl->tbl_purc_grn_dtl_total_amount = $this->addNo($dtl['gross_amount']);
                    $grnDtl->business_id = $business_id;
                    $grnDtl->company_id = $business_id;
                    $grnDtl->branch_id = $branch_id;
                    $grnDtl->tbl_purc_grn_dtl_user_id = $user_id;
                    // calculations
                    $prod_total_qty = (int)$dtl['quantity']+(int)$dtl['foc_qty'];
                    $prod_gross_amount = $this->addNo($dtl['amount'])-$this->addNo($dtl['dis_amount']);
                    $prod_gross_rate = $prod_gross_amount/$prod_total_qty;
                    $prod_rate_expense = $TotalExpAmount/$sumOfProdTotalQty;
                    $prod_net_rate = ($prod_rate_expense+$prod_gross_rate);
                    $grnDtl->dtl_prod_total_qty = $prod_total_qty;
                    $grnDtl->dtl_prod_gross_amount = $prod_gross_amount;
                    $grnDtl->dtl_prod_gross_rate = $prod_gross_rate;
                    $grnDtl->dtl_prod_rate_expense = $prod_rate_expense;
                    $grnDtl->dtl_prod_net_rate = $prod_net_rate;

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
                    $grnDtl->tbl_purc_grn_dtl_rate_inc_foc = $rate_inc_foc;

                    $grnDtl->save();

                    $net_total += $this->addNo($dtl['gross_amount']);
                    $total_gross_amount += $this->addNo($dtl['gross_amount']);
                    $amount_total += $this->addNo($dtl['amount']);
                    $vat_amount_total += $this->addNo($dtl['vat_amount']);
                    $disc_amount_total += $this->addNo($dtl['dis_amount']);

                    $firstBarcodeRate = (float)$prod_net_rate/(float)$barcode->product_barcode_packing;

                    $purc_rate = $this->addNo($dtl['rate']);
                    $purc_rate = (float)$purc_rate/(float)$barcode->product_barcode_packing;
                    $barcodeList = TblPurcProductBarcode::where('product_id',$dtl['product_id'])->get();
                    foreach ($barcodeList as $item){
                        $barcodeRate = (float)$firstBarcodeRate * (float)$item->product_barcode_packing;
                        $barcodePurcRate = (float)$purc_rate * (float)$item->product_barcode_packing;
                        TblPurcProductBarcodePurchRate::where('product_barcode_id',$item->product_barcode_id)
                            ->where('product_id',$item->product_id)
                            ->where('branch_id',auth()->user()->branch_id)->update([
                                'product_barcode_cost_rate'=> $barcodeRate,
                                'product_barcode_purchase_rate'=> $barcodePurcRate,
                            ]);
                    }
                }

            }

            $grnTotal = TblPurcGrn::where('grn_id',$grn->grn_id)->where($currentBCB)->first();
            $grnTotal->grn_total_qty = $sumOfProdTotalQty;
            $grnTotal->grn_total_amount = $total_gross_amount;
            $grnTotal->grn_total_expense_amount = $TotalExpAmount;
            $grnTotal->grn_total_net_amount = $total_gross_amount - $TotalExpAmount;
            $grnTotal->save();
            // insert update grn voucher
            $table_name = 'tbl_acco_voucher';
            if(isset($id)){
                $action = 'update';
                $grn_id = $id;
                $grn = TblPurcGrn::where('grn_id',$grn_id)->where($currentBCB)->first();
                $voucher_id = $grn->voucher_id;
            }else{
                $action = 'add';
                $grn_id = $grn->grn_id;
                $voucher_id = ApiUtilities::uuid();
            }
            $where_clause = '';
            $supplier = TblPurcSupplier::where('supplier_id',$request->supplier_id)->where($currentBC)->first();
            $supplier_chart_account_id = (int)$supplier->supplier_account_id;
            $config = TblDefiConfiguration::first();
            $purchase_discount  =   $config->purchase_discount;
            $purchase_stock     =   $config->purchase_stock;
            $purchase_vat       =   $config->purchase_vat;

            //check account code
            $ChartArr = [
                $supplier_chart_account_id,
                $purchase_discount,
                $purchase_stock,
                $purchase_vat
            ];
            $response = $this->ValidateCharCode($ChartArr);
            if($response == false){
                return $this->ApiJsonErrorResponse($data,'voucher Account Code not correct');
            }

            //voucher start
            $data = [
                'voucher_id'            =>  $voucher_id,
                'voucher_document_id'   =>  $grn_id,
                'voucher_no'            =>  $grn->grn_code,
                'voucher_date'          =>  date('Y-m-d', strtotime($request->grn_date)),
                'voucher_descrip'       =>  'Mob Purchase: '.$grn->grn_remarks .' - Ref:'.$request->grn_bill_no,
                'voucher_type'          =>  strtoupper(self::$prefix),
                'branch_id'             =>  $branch_id,
                'business_id'           =>  $business_id,
                'company_id'            =>  $business_id,
                'voucher_user_id'       =>  $user_id
            ];
            $data['chart_account_id'] = $supplier_chart_account_id;
            $data['voucher_debit'] = 0;
            $data['voucher_credit'] = abs($net_total);
            $data['voucher_sr_no'] = 1;
            // for debit entry net_total
            $this->proAccoVoucherInsert($voucher_id,$action,$table_name,$data,$where_clause);

            $action = 'add';
            $discount_chart_account_id = $purchase_discount;
            $data['chart_account_id'] = $discount_chart_account_id;
            $data['voucher_debit'] = 0;
            $data['voucher_credit'] =  abs($disc_amount_total);
            $data['voucher_sr_no'] = 2;
            // for debit entry disc_amount_total
            $this->proAccoVoucherInsert($voucher_id,$action,$table_name,$data,$where_clause);

            $action = 'add';
            $stock_chart_account_id = $purchase_stock;
            $data['chart_account_id'] = $stock_chart_account_id;
            $data['voucher_debit'] = abs($amount_total);
            $data['voucher_credit'] = 0;
            $data['voucher_sr_no'] = 3;
            // for credit entry amount_total
            $this->proAccoVoucherInsert($voucher_id,$action,$table_name,$data,$where_clause);

            $action = 'add';
            $vat_payable_chart_account_id = $purchase_vat;
            $data['chart_account_id'] = $vat_payable_chart_account_id;
            $data['voucher_debit'] = abs($vat_amount_total);
            $data['voucher_credit'] = 0;
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
                $grn = TblPurcGrn::where('grn_id',$grn_id)->first();
                $grn->voucher_id = $voucher_id;
                $grn->save();
            }

            // end insert update grn voucher

        }catch (QueryException $e) {
            DB::rollback();
            return $this->ApiJsonErrorResponse($data, $e->getMessage());
        } catch (ModelNotFoundException $e) {
            DB::rollback();
            return $this->ApiJsonErrorResponse($data, $e->getMessage());
        } catch (ValidationException $e) {
            DB::rollback();
            return $this->ApiJsonErrorResponse($data, $e->getMessage());
        } catch (Exception $e) {
            DB::rollback();
            return $this->ApiJsonErrorResponse($data, $e->getMessage());
        }
        DB::commit();
        if(isset($id)){
            $data = (object)[];
            return $this->ApiJsonSuccessResponse($data,trans('message.update'));
        }else{
            $data = (object)[];
            return $this->ApiJsonSuccessResponse($data,trans('message.create'));
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

    public function FilterData($current){
        $grn_dtl = [];
        $i=0;
        foreach($current->grn_dtl as $dtl){
            $grn_dtl [$i]["purc_grn_dtl_id"]= $dtl->purc_grn_dtl_id;
            $grn_dtl [$i]["product_id"]= $dtl->product->product_id;
            $grn_dtl [$i]["product_name"]= $dtl->product->product_name;
            $grn_dtl [$i]["product_barcode_id"]= $dtl->barcode->product_barcode_id;
            $grn_dtl [$i]["pd_barcode"]= $dtl->barcode->product_barcode_barcode;
            $grn_dtl [$i]["uom_id"]= $dtl->uom->uom_id;
            $grn_dtl [$i]["uom_name"]= $dtl->uom->uom_name;
            $grn_dtl [$i]["pd_packing"]= $dtl->barcode->product_barcode_packing;
            $grn_dtl [$i]["quantity"]= ($dtl->tbl_purc_grn_dtl_quantity != null)?$dtl->tbl_purc_grn_dtl_quantity:"";
            $grn_dtl [$i]["foc_qty"]= ($dtl->tbl_purc_grn_dtl_foc_quantity != null)?$dtl->tbl_purc_grn_dtl_foc_quantity:"";
            $grn_dtl [$i]["fc_rate"]= ($dtl->tbl_purc_grn_dtl_fc_rate != null)?$dtl->tbl_purc_grn_dtl_fc_rate:"";
            $grn_dtl [$i]["rate"]= ($dtl->tbl_purc_grn_dtl_rate != null)?$dtl->tbl_purc_grn_dtl_rate:"";
            $grn_dtl [$i]["amount"]= ($dtl->tbl_purc_grn_dtl_amount != null)?$dtl->tbl_purc_grn_dtl_amount:"";
            $grn_dtl [$i]["dis_perc"]= ($dtl->tbl_purc_grn_dtl_disc_percent != null)?$dtl->tbl_purc_grn_dtl_disc_percent:"";
            $grn_dtl [$i]["dis_amount"]= ($dtl->tbl_purc_grn_dtl_disc_amount != null)?$dtl->tbl_purc_grn_dtl_disc_amount:"";
            $grn_dtl [$i]["vat_perc"]= ($dtl->tbl_purc_grn_dtl_vat_percent != null)?$dtl->tbl_purc_grn_dtl_vat_percent:"";
            $grn_dtl [$i]["vat_amount"]= ($dtl->tbl_purc_grn_dtl_vat_amount != null)?$dtl->tbl_purc_grn_dtl_vat_amount:"";
            $grn_dtl [$i]["batch_no"]= !empty($dtl->tbl_purc_grn_dtl_batch_no)?$dtl->tbl_purc_grn_dtl_batch_no:"";

            $prod_date= date('d-m-Y', strtotime(trim(str_replace('/','-',$dtl->tbl_purc_grn_dtl_production_date))));
            $grn_dtl [$i]["production_date"]= ($prod_date =='01-01-1970')?'':$prod_date;

            $expiry_date= date('d-m-Y', strtotime(trim(str_replace('/','-',$dtl->tbl_purc_grn_dtl_expiry_date))));
            $grn_dtl [$i]["expiry_date"]= ($expiry_date =='01-01-1970')?'':$expiry_date;
            $grn_dtl [$i]["gross_amount"]= ($dtl->tbl_purc_grn_dtl_total_amount != null)?$dtl->tbl_purc_grn_dtl_total_amount:"";;
            $i++;
        }

        $exp = [];
        $x=0;
        foreach($current->grn_expense as $expense){
            $exp [$x]["account_id"]= $expense->accounts->chart_account_id;
            $exp [$x]["account_code"]= $expense->accounts->chart_code;
            $exp [$x]["account_name"]= $expense->accounts->chart_name;
            $exp [$x]["expense_amount"]= $expense->grn_expense_amount;
            $x++;
        }
        if(count($exp) == 0){$exp = (object)[];}

        $object = (object) [
            "business_id" 		=> 	$current->business_id,
            "branch_id" 		=> 	$current->branch_id,
            "user_id" 			=> 	$current->grn_user_id,
            "grn_id"            =>  $current->grn_id,
            "grn_code"            =>  $current->grn_code,
            "grn_date"			=> 	date('d-m-Y', strtotime(trim(str_replace('/','-',$current->grn_date)))),
            "supplier_id" 		=> 	isset($current->supplier->supplier_id)?$current->supplier->supplier_id:'',
            "supplier_name" 	=> 	isset($current->supplier->supplier_name)?$current->supplier->supplier_name:'',
            "purchase_order_id" => 	isset($current->PO->purchase_order_id)?$current->PO->purchase_order_id:'',
            "purchase_order_code" => 	isset($current->PO->purchase_order_code)?$current->PO->purchase_order_code:'',
            "grn_bill_no"		=>	$current->grn_bill_no,
            "payment_type_id"	=>	$current->payment_type_id,
            "payment_term_id"	=>	$current->grn_ageing_term_id,
            "payment_term_val"	=>	$current->grn_ageing_term_value,
            "store_id"			=>	$current->store_id,
            "currency_id"		=>	$current->currency_id,
            "exchange_rate" 	=> 	$current->grn_exchange_rate,
            "notes" 			=> 	$current->grn_remarks,
            "total_expenses"	=> 	$current->grn_total_expense_amount,
            "pd"                =>  $grn_dtl,
            "pdsm"              =>  $exp
        ];

        return $object;
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
            $grn = TblPurcGrn::where('grn_id',$id)->where('grn_type',strtoupper(self::$prefix))->where(Utilities::currentBCB())->first();
            $voucher_id = $grn->voucher_id;
            if(!empty($voucher_id)){
                $this->proAccoVoucherDelete($voucher_id);
            }
            $grn->grn_dtl()->delete();
            $grn->delete();

        }catch (QueryException $e) {
            DB::rollback();
            return $this->ApiJsonErrorResponse($data, $e->getMessage());
        } catch (ModelNotFoundException $e) {
            DB::rollback();
            return $this->ApiJsonErrorResponse($data, $e->getMessage());
        } catch (ValidationException $e) {
            DB::rollback();
            return $this->ApiJsonErrorResponse($data, $e->getMessage());
        } catch (Exception $e) {
            DB::rollback();
            return $this->ApiJsonErrorResponse($data, $e->getMessage());
        }
        DB::commit();
        return $this->ApiJsonSuccessResponse($data, trans('message.delete'));
    }
}
