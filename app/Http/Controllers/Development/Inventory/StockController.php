<?php

namespace App\Http\Controllers\Inventory;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Library\Utilities;
use App\Models\TblAccoVoucher;
use App\Models\TblDefiConfigBranches;
use App\Models\TblDefiPaymentType;
use App\Models\TblDefiStore;
use App\Models\TblInveStock;
use App\Models\TblInveStockDtl;
use App\Models\TblPurcDemand;
use App\Models\TblPurcDemandDtl;
use App\Models\TblPurcProductBarcode;
use App\Models\TblPurcProductBarcodeDtl;
use App\Models\TblInveItemFormulation;
use App\Models\TblPurcGrn;
use App\Models\TblPurcProductBarcodePurchRate;
use App\Models\TblPurcProductBarcodeSaleRate;
use App\Models\TblPurcSupplier;
use App\Models\TblSoftBranch;
use App\Models\ViewInveDisplayLocation;
use App\Models\ViewInveStock;
use App\Models\TblAccCoa;


use Illuminate\Http\Request;

// db and Validator
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Importer;

class StockController extends Controller
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

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($type,$id = null)
    {
        /*
         * Opening Stock = OS
         * Stock Transfer = ST
         * Stock Receiving = SR
         * Stock Adjustment = SA
         * Expired Items = EI
         * Sample Items = SI
         * Damaged Items = DI
         * Repair Items = RI
         * Internal Stock Transfer = ST
         * */
        $locale = Session::get('locale');
        //dd($locale);
        $data['page_data'] = [];
        $data['type'] = $type;
        switch ($type){
            case 'opening-stock': {
                $data['page_data']['title'] = 'Opening Stock';
                $formUrl = 'opening_stock';
                $data['stock_code_type'] = 'os';
                $data['stock_menu_id'] = '54';
                break;
            }
            case 'stock-transfer': {
                $data['page_data']['title'] = 'Stock Transfer';
                $formUrl = 'stock_transfer';
                $data['stock_code_type'] = 'st';
                $data['stock_menu_id'] = '65';
                break;
            }
            case 'stock-adjustment': {
                $data['page_data']['title'] = 'Stock Adjustment';
                $formUrl = 'stock_adjustment';
                $data['stock_code_type'] = 'sa';
                $data['stock_menu_id'] = '55';
                break;
            }

            case 'damaged-items': {
                $data['page_data']['title'] = 'Damaged Items';
                $formUrl = 'stock_item';
                $data['stock_code_type'] = 'di';
                $data['stock_menu_id'] = '57';
                break;
            }
            case 'expired-items': {
                $data['page_data']['title'] = 'Expired Items';
                $formUrl = 'stock_item';
                $data['stock_code_type'] = 'ei';
                $data['stock_menu_id'] = '58';
                break;
            }
            case 'sample-items': {
                $data['page_data']['title'] = 'Sample Items';
                $formUrl = 'stock_item';
                $data['stock_code_type'] = 'sp';
                $data['stock_menu_id'] = '56';
                break;
            }
            case 'repair-items': {
                $data['page_data']['title'] = 'Repair Items';
                $formUrl = 'stock_item';
                $data['stock_code_type'] = 'ri';
                $data['stock_menu_id'] = '183';
                break;
            }
            case 'stock-receiving': {
                $data['page_data']['title'] = 'Stock Receiving';
                $formUrl = 'stock_receiving';
                $data['stock_code_type'] = 'str';
                $data['stock_menu_id'] = '76';
                break;
            }
            case 'internal-stock-transfer': {
                $data['page_data']['title'] = 'Internal Stock Transfer';
                $formUrl = 'internal_stock_transfer';
                $data['stock_code_type'] = 'ist';
                $data['stock_menu_id'] = '131';
                break;
            }
            case 'disassemble-products': {
                $data['page_data']['title'] = 'Disassemble Product';
                $formUrl = 'assemble_products';
                $data['stock_code_type'] = 'dss';
                $data['stock_menu_id'] = '134';
                break;
            }
        }
        $data['form_type'] = $type;
        $data['page_data']['path_index'] = $this->prefixIndexPage.'stock/'.$type;
        $data['page_data']['create'] = '/stock/'.$type.$this->prefixCreatePage;
        if(isset($id)){
            if(TblInveStock::where('stock_id','LIKE',$id)->where(Utilities::currentBCB())->exists()){
                $data['page_data'] = array_merge($data['page_data'], Utilities::editForm());
                $data['permission'] = $data['stock_menu_id'].'-edit';
                $data['id'] = $id;
                $data['current'] = TblInveStock::with('stock_dtls','product','barcode','supplier','formulation')->where(Utilities::currentBCB())->where('stock_id',$id)->where('stock_code_type',$data['stock_code_type'])->first();
                $data['current_audit'] = TblInveStock::with('audit_stock_dtls','product','barcode')->where(Utilities::currentBCB())->where('stock_id',$id)->where('stock_code_type',$data['stock_code_type'])->first();

               // dd($data['current']->toArray());
                $data['display_location'] = ViewInveDisplayLocation::where('branch_id',auth()->user()->branch_id)->where('store_id',$data['current']->stock_store_from_id)->orderBy('display_location_name_string')->get();
                $data['page_data']['print'] = '/stock/'.$type.'/print/'.$id;
                $data['stock_code'] = $data['current']->stock_code;
                if($type == 'stock-transfer' && $data['current']->stock_receive_status == 1){
                    //$data['page_data']['action'] = '';
                }
            }else{
                abort('404');
            }
        }else{
            $data['permission'] = $data['stock_menu_id'].'-create';
            $data['page_data'] = array_merge($data['page_data'], Utilities::newForm());
            $doc_data = [
                'biz_type'          => 'branch',
                'model'             => 'TblInveStock',
                'code_field'        => 'stock_code',
                'code_prefix'       => strtoupper($data['stock_code_type']),
                'code_type_field'   => 'stock_code_type',
                'code_type'         => $data['stock_code_type']

            ];
            $data['stock_code'] = Utilities::documentCode($doc_data);
        }
        $data['rate_types'] = config('constants.rate_type');
        $data['store'] = TblDefiStore::where(Utilities::currentBCB())->get();
        $data['branch'] = TblSoftBranch::where(Utilities::currentBC())->where('branch_id','!=',auth()->user()->branch_id)->get();
        $data['rate_by'] = config('constants.rate_by');
        $data['rate_types'] = config('constants.rate_type');
        $data['payment_type'] = TblDefiPaymentType::where(Utilities::currentBC())->where('payment_type_entry_status',1)->get();

        $arr = [
            'biz_type' => 'branch',
            'code' => $data['stock_code'],
            'link' => $data['page_data']['create'],
            'table_name' => 'tbl_inve_stock',
            'col_id' => 'stock_id',
            'col_code' => 'stock_code',
            'code_type_field'   => 'stock_code_type',
            'code_type'         => $data['stock_code_type'],
        ];
        $data['switch_entry'] = $this->switchEntry($arr);

        return view('inventory.'.$formUrl.'.form',compact('data'));
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request,$type, $id = null)
    {
        //dd($request);
        $data = [];
        $errData = [];
        $valid = [
            // 'stock_date' => 'required|date_format:d-m-Y',
            'store' => 'nullable|numeric',
            // 'store_to' => 'nullable|numeric',
            // 'pd.*.product_id' => 'nullable|numeric',
            // 'pd.*.product_barcode_id' => 'nullable|numeric',
            // 'pd.*.uom_id' => 'nullable|numeric',
            // 'pd.*.pd_barcode' => 'nullable|max:100',
            // 'pd.*.demand_qty' => 'nullable|numeric',
            // 'pd.*.quantity' => 'nullable|numeric',
            // 'pd.*.batch_no' => 'nullable|max:20',
        ];
        if($request->stock_code_type == 'os'){
            $valid['store'] = 'required|numeric|not_in:0';
        }
        //dd($valid);
        $validator = Validator::make($request->all(), $valid);
        if ($validator->fails()) {
            $data['validator_errors'] = $validator->errors();
            return $this->jsonErrorResponse($data, trans('message.required_fields'), 422);
        }
        if(isset($request->selected_barcode_rate) && !empty($request->selected_barcode_rate)){
            $rate_by = config('constants.rate_by');
            $rate_by=array_flip($rate_by);
            if(!in_array($request->selected_barcode_rate,$rate_by)){
                return $this->jsonErrorResponse($data, trans('message.required_fields'), 422);
            }
        }
        if(empty($request->pd)){
            return $this->returnjsonerror("Please Enter Product Detail",201);
        }
        DB::beginTransaction();
        try{
            $formType = $request->stock_code_type;
            // dd($formType);
            if(isset($id)){
                $stock = TblInveStock::where('stock_id',$id)->first();
                TblAccoVoucher::where('voucher_document_id',$id)->where(Utilities::currentBCB())->delete();
            }else{
                if($formType == 'str'){
                    $stockTrans = TblInveStock::where('stock_id',$request->stock_from_id)
                        ->where('stock_receive_status',0)
                        ->where('stock_code_type','st')->first();
                    if(empty($stockTrans)){
                        return $this->returnjsonerror("Already stock received.",200);
                    }
                    $stockTrans->stock_receive_status =  1;
                    $stockTrans->save();
                }
                $stock = new TblInveStock();
                $stock->stock_id =  Utilities::uuid();
                $doc_data = [
                    'biz_type'          => 'branch',
                    'model'             => 'TblInveStock',
                    'code_field'        => 'stock_code',
                    'code_prefix'       => strtoupper($formType),
                    'code_type_field'   => 'stock_code_type',
                    'code_type'         => $formType

                ];
                $stock->stock_code =  Utilities::documentCode($doc_data);
            }
            $form_id = $stock->stock_id;
            $stock->stock_code_type =  $formType;
            $stock->stock_date =   date('Y-m-d', strtotime($request->stock_date));
            $stock->stock_menu_id =  $request->stock_menu_id;
            $stock->sales_rate_type =  isset($request->rate_type)?$request->rate_type:"";
            $stock->sales_rate_perc =  isset($request->rate_perc)?$request->rate_perc:"";
            $stock->start_date =  isset($request->start_date)?$request->start_date:"";
            $stock->end_date =  isset($request->end_date)?$request->end_date:"";
            $stock->is_expiry =  isset($request->is_active)?1:0;
            $stock->sale_rate =  isset($request->sale)?$request->sale:"";
            $stock->cost_rate =  isset($request->cost)?$request->cost:"";
            $stock->stock_total_qty =  0;
            $stock->stock_total_amount =  0;
            $stock->product_id = $request->f_product_id;
            $stock->product_barcode_id = $request->f_product_barcode_id;
            // $stock->product_barcode_barcode = $request->f_barcode;
            $stock->formulation_id = isset($request->formulation_id)?$request->formulation_id:"";
            $stock->product_barcode_packing = isset($request->product_barcode_packing)?$request->product_barcode_packing:"";
            $stock->uom_id = isset($request->uom_id)?$request->uom_id:"";
            $stock->product_qty_base_unit = isset($request->assamble_qty)?$request->assamble_qty:0;
            // $stock->formulation_code = isset($request->formulation_code)?$request->formulation_code:"";
            $stock->assamble_qty = isset($request->assamble_qty)?$request->assamble_qty:"";
            $stock->stock_rate_type = isset($request->rate_type)?$request->rate_type:'';

            $stock->stock_rate_perc = isset($request->rate_perc)?$request->rate_perc:'';

            $stock->ref_grn_id = isset($request->grn_id)?$request->grn_id:'';
            $stock->ref_grn_code = isset($request->grn_code)?$request->grn_code:'';
            if($formType == 'os')
            {
                $stock->stock_store_from_id =  isset($request->store)?$request->store:"";
            }
            if($formType == 'str')
            {
                $stock->stock_store_from_id =  isset($request->store)?$request->store:"";
                $stock->stock_store_to_id =  isset($request->store_to)?$request->store_to:"";
                $stock->stock_branch_from_id =  isset($request->branch_from_id)?$request->branch_from_id:"";
                $stock->stock_branch_to_id =  isset($request->branch)?$request->branch:"";
            }
            if($formType == 'st')
            {
                $store = \App\Models\TblDefiStore::where('branch_id',$request->branch_to)->where('store_default_value',1)->first();
                $store_to = isset($store->store_id)?$store->store_id:"";

                $stock->stock_store_from_id =  isset($request->store)?$request->store:"";
                $stock->stock_store_to_id =  isset($store_to)?$store_to:"";
                $stock->stock_branch_from_id =  isset($request->branch)?$request->branch:"";
                $stock->stock_branch_to_id =  isset($request->branch_to)?$request->branch_to:"";
            }
            $stock->sales_sales_type =  isset($request->sales_sales_type)?$request->sales_sales_type:"";
            $stock->stock_rate_by =  isset($request->selected_barcode_rate)?$request->selected_barcode_rate:"";
            $stock->stock_remarks = $request->stock_remarks;
            $stock->stock_request_id = isset($request->stock_from_id)?$request->stock_from_id:'';
            $stock->stock_location_id = isset($request->stock_location_id)?$request->stock_location_id:'';
            $stock->stock_entry_status = 1;
            $stock->supplier_id = isset($request->supplier_id)?$request->supplier_id:'';


            $stock->stock_total_items = $request->summary_total_item;
            $stock->stock_total_gross_net_amount = $request->summary_net_amount;
            $stock->stock_advance_tax_perc = $request->overall_vat_perc;
            $stock->stock_advance_tax_amount = $request->overall_vat_amount;
            $stock->stock_total_net_amount = $request->overall_net_amount;
            $stock_total_net_amount = $request->overall_net_amount;


            $stock->business_id = auth()->user()->business_id;
            $stock->company_id = auth()->user()->company_id;
            $stock->branch_id = auth()->user()->branch_id;
            $stock->stock_user_id = auth()->user()->id;
            $stock->save();

            $del_Dtls = TblInveStockDtl::where('stock_id',$id)->get();
            foreach ($del_Dtls as $del_Dtl){
                TblInveStockDtl::where('stock_dtl_id',$del_Dtl->stock_dtl_id)->delete();
            }
            $stock_total_qty = 0;
            $stock_total_amount = 0;
            $stock_total_vat_amount = 0;
            $stock_total_disc_amount = 0;
            $stock_total_gross_amount = 0;
            if(isset($request->pd)){
                $key = 1;
                foreach($request->pd as $pd){
                    $errData = $pd;
                    $dtl = new TblInveStockDtl();
                    $dtl->stock_id =  $stock->stock_id;
                    $dtl->stock_dtl_id =  Utilities::uuid();
                    $dtl->stock_dtl_sr_no =  $key++;
                    $dtl->product_id =  $pd['product_id'];
                    $dtl->product_barcode_id =  $pd['product_barcode_id'];
                    $dtl->product_barcode_barcode =  $pd['pd_barcode'];
                    $dtl->uom_id =  $pd['uom_id'];
                    $dtl->stock_dtl_packing = isset($pd['pd_packing'])?$pd['pd_packing']:'';
                    if($formType == 'sa'){
                        $adjustmentQty = (float)$pd['physical_quantity'] - (float)$pd['stock_quantity'];
                        $dtl->stock_dtl_quantity = $adjustmentQty; // adjustment qty
                    }else{
                        $dtl->stock_dtl_quantity =  $this->addNo($pd['quantity']);
                    }
                    if($formType == 'st')
                    {
                        $per_itm_gst_amount = @round($pd['gst_amount'] / $pd['grn_qty'],2);
                        $gst_amount = $per_itm_gst_amount * $pd['quantity'];



                        $per_itm_dis_amount = @round($pd['dis_amount'] / $pd['grn_qty'],2);
                        $dis_amount = $per_itm_dis_amount * $pd['quantity'];

                        $per_itm_after_dis_amount = @round($pd['after_dis_amount'] / $pd['grn_qty'],2);
                        $after_dis_amount = $per_itm_after_dis_amount * $pd['quantity'];

                        $per_itm_fed_amount = @round($pd['fed_amount'] / $pd['grn_qty'],2);
                        $fed_amount = $per_itm_fed_amount * $pd['quantity'];

                        $per_itm_spec_disc_amount = @round($pd['spec_disc_amount'] / $pd['grn_qty'],2);
                        $spec_disc_amount = $per_itm_spec_disc_amount * $pd['quantity'];

                        $dtl->grn_qty =  isset($pd['grn_qty'])?$this->addNo($pd['grn_qty']):"";
                        $dtl->grn_disc_per =  isset($pd['dis_perc'])?$this->addNo($pd['dis_perc']):"";
                        $dtl->grn_disc_amount =  isset($dis_amount)?$this->addNo($dis_amount):"";
                        $dtl->grn_after_disc_amount =  isset($after_dis_amount)?$this->addNo($after_dis_amount):"";
                        $dtl->grn_gst_per =  isset($pd['gst_perc'])?$this->addNo($pd['gst_perc']):"";
                        $dtl->grn_gst_amount =  isset($gst_amount)?$this->addNo($gst_amount):"";
                        $dtl->grn_fed_per =  isset($pd['fed_perc'])?$this->addNo($pd['fed_perc']):"";
                        $dtl->grn_fed_amount =  isset($fed_amount)?$this->addNo($fed_amount):"";
                        $dtl->grn_spec_disc_per =  isset($pd['spec_disc_perc'])?$this->addNo($pd['spec_disc_perc']):"";
                        $dtl->grn_spec_disc_amount =  isset($spec_disc_amount)?$this->addNo($spec_disc_amount):"";
                        $dtl->grn_gross_amount =  isset($pd['gross_amount'])?$this->addNo($pd['gross_amount']):"";
                        $dtl->grm_net_amount =  isset($pd['net_amount'])?$this->addNo($pd['net_amount']):"";
                        $dtl->grn_rate =  isset($pd['unit_price'])?$this->addNo($pd['unit_price']):"";
                    }
                    $dtl->stock_dtl_qty_base_unit =  $this->addNo($pd['quantity']);
                    $dtl->stock_dtl_rate =  isset($pd['rate'])?$this->addNo($pd['rate']):"";
                    $dtl->stock_dtl_sale_rate =  isset($pd['sale_rate'])?$this->addNo($pd['sale_rate']):"";
                    $dtl->stock_dtl_sale_amount =  isset($pd['sale_amount'])?$this->addNo($pd['sale_amount']):"";
                    //$dtl->cost_rate =  isset($pd['cost_rate'])?$this->addNo($pd['cost_rate']):"";

                    /*if($formType == 'st'){
                        if(round($pd['hidden_cost_rate']) != 0){
                            $dtl->cost_rate =  isset($pd['hidden_cost_rate'])?$this->addNo($pd['hidden_cost_rate']):"";
                            $dtl->cost_posting =  isset($pd['hidden_cost_rate'])?1:0;

                            $cost_amount = $pd['hidden_cost_rate'] * $pd['quantity'];
                            $dtl->cost_amount =  isset($cost_amount)?$this->addNo($cost_amount):"";
                        }
                    }*/

                    if($formType == 'st')
                    {
                        $dtl->stock_dtl_ex_net_tp =  isset($pd['ex_net_tp'])?$this->addNo($pd['ex_net_tp']):"";
                        $dtl->stock_dtl_adjrate =  isset($pd['adjrate'])?$this->addNo($pd['adjrate']):"0";
                        $dtl->stock_dtl_sys_quantity = isset($pd['sys_qty'])?$this->addNo($pd['sys_qty']):"";
                    }

                    $dtl->stock_dtl_purc_rate =  isset($pd['purc_rate'])?$this->addNo($pd['purc_rate']):"";
                    $dtl->stock_dtl_demand_quantity =  isset($pd['demand_qty'])?$this->addNo($pd['demand_qty']):"";
                    $dtl->stock_dtl_stock_transfer_qty =  isset($pd['stock_transfer_qty'])?$this->addNo($pd['stock_transfer_qty']):"";
                    //add new column formula qty
                    $dtl->stock_dtl_formula_qty = isset($pd['formula_qty'])?$this->addNo($pd['formula_qty']): 0;
                    $dtl->stock_dtl_batch_no =  isset($pd['batch_no'])?$pd['batch_no']:"";
                    $dtl->mrp =  isset($pd['mrp'])?$this->addNo($pd['mrp']):"";
                    $dtl->stock_dtl_store =  isset($pd['pd_store'])?$pd['pd_store']:"";
                    $dtl->stock_dtl_amount =  isset($pd['amount'])?$this->addNo($pd['amount']):"";
                    $dtl->stock_dtl_disc_percent =  isset($pd['dis_perc'])?$this->addNo($pd['dis_perc']):"";
                    $dtl->stock_dtl_disc_amount =  isset($pd['dis_amount'])?$this->addNo($pd['dis_amount']):"";
                    $dtl->stock_dtl_vat_percent =  isset($pd['vat_perc'])?$this->addNo($pd['vat_perc']):"";
                    $dtl->stock_dtl_vat_amount =  isset($pd['vat_amount'])?$this->addNo($pd['vat_amount']):"";
                    $dtl->stock_dtl_total_amount =  isset($pd['gross_amount'])?$this->addNo($pd['gross_amount']):"";
                    $dtl->stock_dtl_stock_quantity =  isset($pd['stock_quantity'])?$this->addNo($pd['stock_quantity']):"";
                    $dtl->stock_dtl_physical_quantity =  isset($pd['physical_quantity'])?$this->addNo($pd['physical_quantity']):"";
                    $dtl->stock_dtl_production_date =  isset($pd['production_date'])?date('Y-m-d', strtotime($pd['production_date'])):"";
                    $dtl->stock_dtl_expiry_date =  isset($pd['expiry_date'])?date('Y-m-d', strtotime($pd['expiry_date'])):"";
                    $dtl->business_id = auth()->user()->business_id;
                    $dtl->company_id = auth()->user()->company_id;
                    $dtl->branch_id = auth()->user()->branch_id;
                    $dtl->dtl_user_id = auth()->user()->id;
                    $stock_total_qty += isset($pd['quantity'])?$this->addNo($pd['quantity']):0;
                    $stock_total_disc_amount += isset($pd['dis_amount'])?$this->addNo($pd['dis_amount']):0;
                    $stock_total_vat_amount += isset($pd['vat_amount'])?$this->addNo($pd['vat_amount']):0;
                    $stock_total_amount += isset($pd['amount'])?$this->addNo($pd['amount']):0;
                    $stock_total_gross_amount += isset($pd['gross_amount'])?$this->addNo($pd['gross_amount']):0;

                   // dd($dtl);
                    $dtl->save();
                    if($formType == 'st'){
                        if($pd['demand_qty'] <= $pd['quantity']){
                            $demnadDtl = TblPurcDemandDtl::where('demand_id',$request->stock_from_id)
                                ->where('product_id',$pd['product_id'])
                                ->where('product_barcode_id',$pd['product_barcode_id'])->first();
                            if(!empty($demnadDtl)){
                                $demnadDtl->demand_dtl_approve_status = 'approved';
                                $demnadDtl->save();
                            }
                        }
                        if($pd['demand_qty'] > $pd['quantity']){
                            $demnadDtl = TblPurcDemandDtl::where('demand_id',$request->stock_from_id)
                                ->where('product_id',$pd['product_id'])
                                ->where('product_barcode_id',$pd['product_barcode_id'])->first();
                            if(!empty($demnadDtl)){
                                $demnadDtl->demand_dtl_approve_status = 'pending';
                                $demnadDtl->save();
                            }
                        }
                    }
                    if($request->store == 2 && $formType == 'os'){
                        // if select store *showroom* and Stock Location
                        // then update product with this stock location and user
                        $branches = TblSoftBranch::where('branch_active_status',1)->get();
                        $barcodes = TblPurcProductBarcode::where('product_id',$pd['product_id'])->get();
                        foreach($barcodes as $barcode){
                            foreach($branches as $branch){
                                if($barcode->product_barcode_id == $pd['product_barcode_id'] && $branch->branch_id == auth()->user()->branch_id){
                                    if(TblPurcProductBarcodeDtl::where('product_barcode_id',$pd['product_barcode_id'])->where('branch_id',auth()->user()->branch_id)->exists()){
                                        $barcodeDtl = TblPurcProductBarcodeDtl::where('product_barcode_id',$pd['product_barcode_id'])
                                            ->where('branch_id',auth()->user()->branch_id)->first();
                                    }else{
                                        $barcodeDtl = new TblPurcProductBarcodeDtl();
                                        $barcodeDtl->product_barcode_dtl_id = Utilities::uuid();
                                        $barcodeDtl->product_barcode_id = $pd['product_barcode_id'];
                                        $barcodeDtl->branch_id = auth()->user()->branch_id;
                                        $barcodeDtl->product_barcode_stock_limit_neg_stock = 0;
                                        $barcodeDtl->product_barcode_stock_limit_limit_apply = 0;
                                        $barcodeDtl->product_barcode_stock_limit_status = 0;
                                        $barcodeDtl->product_barcode_tax_apply =  0;
                                    }
                                    $barcodeDtl->product_barcode_shelf_stock_location = $request->stock_location_id;
                                    $barcodeDtl->product_barcode_shelf_stock_sales_man = auth()->user()->id;
                                    $barcodeDtl->save();
                                }else{
                                    TblPurcProductBarcodeDtl::create([
                                        'product_barcode_dtl_id' => Utilities::uuid(),
                                        'product_barcode_id' => $barcode->product_barcode_id,
                                        'branch_id' => $branch->branch_id,
                                        'product_barcode_shelf_stock_location' => 0,
                                        'product_barcode_shelf_stock_sales_man' => '',
                                        'product_barcode_stock_limit_neg_stock' => 0,
                                        'product_barcode_stock_limit_limit_apply' => 0,
                                        'product_barcode_stock_limit_status' => 0,
                                        'product_barcode_tax_apply' => 0,
                                    ]);
                                }
                            }
                        }
                    }

                    if($formType == 'str')
                    {
                        $rateupdate = false;
                        if(TblInveStock::where('stock_id','LIKE',$id)->where(Utilities::currentBCB())->exists())
                        {
                            $rateupdate = true;
                        }

                        //$row = TblSoftBranch::where('branch_id',auth()->user()->branch_id)->first();
                        $row = TblSoftBranch::where('branch_id',$request->branch_from_id)->first();
                        $branch_type = $row->branch_type;

                        // Update Product Prices
                        if($rateupdate && $branch_type == '2')
                        {

                            $pRate = TblPurcProductBarcodePurchRate::where('product_id' , $pd['product_id'])->first();

                            if(!isset($pRate->product_barcode_purchase_rate) || $pRate->product_barcode_purchase_rate == "0"){
                                $purc_rate = $this->addNo($pd['purc_rate']);
                                $purc_rate = (float)$purc_rate/(float)$pd['pd_packing'];

                                $barcodeList = TblPurcProductBarcode::where('product_id',$pd['product_id'])
                                ->where('business_id',auth()->user()->business_id)
                                ->get(['product_id','product_barcode_id','business_id','product_barcode_barcode']);

                                foreach ($barcodeList as $item){
                                    $barcodePurcRate = (float)$purc_rate * (float)$item->product_barcode_packing;

                                    $vExist = TblPurcProductBarcodePurchRate::where('product_barcode_id',$item->product_barcode_id)
                                    ->where('product_id',$item->product_id)
                                    ->where('branch_id',auth()->user()->branch_id);
                                    if($vExist->exists()){
                                        $vExist->update([
                                            'product_barcode_cost_rate'=> $barcodePurcRate,
                                            'product_barcode_purchase_rate'=> $barcodePurcRate,
                                            'net_tp'=> $this->addNo($pd['purc_rate']),
                                            'last_tp'=> $this->addNo($pd['purc_rate']),
                                            'mrp'=> isset($pd['mrp'])?$this->addNo($pd['mrp']):"",
                                        ]);
                                    }else{
                                        $PurchRate = new TblPurcProductBarcodePurchRate();
                                        $PurchRate->product_barcode_purch_id = Utilities::uuid();
                                        $PurchRate->product_id = $item->product_id;
                                        $PurchRate->product_barcode_id = $item->product_barcode_id;
                                        $PurchRate->product_barcode_barcode = $item->product_barcode_barcode;
                                        $PurchRate->product_barcode_purchase_rate = $barcodePurcRate;
                                        $PurchRate->product_barcode_cost_rate = $barcodePurcRate;
                                        $PurchRate->product_barcode_avg_rate = $barcodePurcRate;
                                        $PurchRate->net_tp = $this->addNo($pd['purc_rate']);
                                        $PurchRate->last_tp = $this->addNo($pd['purc_rate']);
                                        $PurchRate->mrp = isset($pd['mrp'])?$this->addNo($pd['mrp']):"";
                                        $PurchRate->business_id = auth()->user()->business_id;
                                        $PurchRate->company_id = auth()->user()->company_id;
                                        $PurchRate->branch_id = auth()->user()->branch_id;
                                        $PurchRate->save();
                                    }
                                }
                            }
                        }
                    }
                }
            }
            $stock = TblInveStock::where('stock_id',$stock->stock_id)->first();
            $stock->stock_total_qty =  $stock_total_qty;
            if($formType == 'st'){
                $stock->stock_total_amount =  $stock_total_gross_amount;
            }else{
                $stock->stock_total_amount =  $stock_total_amount;
            }
            $stock->save();

           // dd($formType);

            if($formType == 'st' || $formType == 'str'){
                // insert update stock transfer voucher
                $table_name = 'tbl_acco_voucher';
                if(isset($id)){
                    $action = 'update';
                    $stock_id = $id;
                    $stock = TblInveStock::where('stock_id',$stock_id)->where(Utilities::currentBCB())->first();
                    $voucher_id = (int)$stock->voucher_id;
                    if(empty($voucher_id)){
                        $action = 'add';
                        $voucher_id = Utilities::uuid();
                    }
                }else{
                    $action = 'add';
                    $stock_id = $stock->stock_id;
                    $voucher_id = Utilities::uuid();
                }

                $cost_amount=0;
                $inner_Dtls = TblInveStockDtl::where('stock_id',$id)->get();
                foreach ($inner_Dtls as $inner_Dtl){
                    $costamount = TblInveStockDtl::where('stock_id',$inner_Dtl->stock_id)->where(Utilities::currentBCB())->first();
                    $cost_amount += $costamount->cost_amount;
                }
               // dd($request);
                $where_clause = '';
                switch ($formType) {
                    case "st":
                        //$Newaccount = TblDefiConfigBranches::where(Utilities::currentB())->where('acc_branch_id',$request->branch_to)->first();
                        $Newaccount = TblDefiConfigBranches::where('acc_branch_id',$request->branch_to)->first();

                        $stock_income = Session::get('dataSession')->stock_transfer_income;
                        $stock_account = Session::get('dataSession')->stock_transfer_stock;
                        $stock_branch_account = $Newaccount->stock_transfer_branch;
                        $stock_vat_account = Session::get('dataSession')->stock_transfer_vat;
                        $stock_disc_account = Session::get('dataSession')->stock_transfer_discount;
                        if(isset($request->sales_sales_type) && $request->sales_sales_type == 1){
                            $stock_cash_account = Session::get('dataSession')->stock_transfer_cash;
                        }else{
                            $branch_account_code = TblSoftBranch::where('branch_id',$request->branch_to)->first('branch_account_code');
                            $stock_cash_account = $branch_account_code->branch_account_code;
                        }
                        break;
                    case "str":
                        $Newaccount = TblDefiConfigBranches::where('acc_branch_id',$request->branch_from_id)->first();
                        $stock_income="";
                        $stock_account = Session::get('dataSession')->store_receive_stock;
                        $stock_branch_account = $Newaccount->stock_transfer_branch;//Session::get('dataSession')->stock_receive_branch;
                        $stock_vat_account = Session::get('dataSession')->stock_receive_vat;
                        $stock_disc_account = Session::get('dataSession')->stock_receive_discount;
                        if(isset($request->sales_sales_type) && $request->sales_sales_type == 1){
                            $stock_cash_account = Session::get('dataSession')->stock_receive_cash;
                        }else{
                            $branch_account_code = TblSoftBranch::where('branch_id',$request->branch)->first('branch_account_code');
                            $stock_cash_account = $branch_account_code->branch_account_code;
                        }
                        break;
                }

                //check account code
                $ChartArr = [
                    $stock_income,
                    $stock_branch_account,
                    $stock_account,
                    $stock_vat_account,
                    $stock_disc_account,
                    $stock_cash_account
                ];
                $response = $this->ValidateCharCode($ChartArr);
                if($response == false){
                    //return $this->jsonErrorResponse($data, "Voucher Account Code not correct",404);
                }
                //voucher start
                $data = [
                    'voucher_id'            =>  $voucher_id,
                    'voucher_document_id'   =>  $stock_id,
                    'voucher_no'            =>  $stock->stock_code,
                    'voucher_date'          =>  date('Y-m-d', strtotime($request->stock_date)),
                    'voucher_type'          =>  strtoupper($formType),
                    'branch_id'             =>  auth()->user()->branch_id,
                    'business_id'           =>  auth()->user()->business_id,
                    'company_id'            =>  auth()->user()->company_id,
                    'voucher_user_id'       =>  auth()->user()->id
                ];

                if($stock_income != "")
                {
                    $action = 'add';
                    $data['chart_account_id'] = $stock_income;
                    switch ($formType) {
                        case "st":
                            $data['voucher_sr_no'] = 1;
                            $data['voucher_descrip'] = 'Stock Transfer Income A/c against inv#'.$stock->stock_code;
                            $data['voucher_debit'] = 0;
                            $data['voucher_credit'] = (abs($stock_total_amount) + abs($cost_amount));
                            break;
                    }
                    // for entry net_total
                    $this->proAccoVoucherInsert($voucher_id,$action,$table_name,$data,$where_clause);
                }

                if($stock_account != "")
                {
                    $action = 'add';
                    $data['chart_account_id'] = $stock_account;
                    switch ($formType) {
                        case "st":
                            $data['voucher_sr_no'] = 2;
                            $data['voucher_descrip'] = 'Stock Transfer Stock A/c against inv#'.$stock->stock_code;
                            $data['voucher_debit'] = 0;
                            $data['voucher_credit'] = abs($cost_amount);
                        break;
                        case "str":
                            $data['voucher_sr_no'] = 1;
                            $data['voucher_descrip'] = 'Stock Receive against inv#'.$stock->stock_code;
                            $data['voucher_debit'] = abs($stock_total_amount);
                            $data['voucher_credit'] = 0;
                        break;
                    }
                    // for entry net_total
                    $this->proAccoVoucherInsert($voucher_id,$action,$table_name,$data,$where_clause);
                }

                if($stock_branch_account != "")
                {
                    $action = 'add';
                    $data['chart_account_id'] = $stock_branch_account;
                    switch ($formType) {
                        case "st":
                            $data['voucher_sr_no'] = 3;
                            $data['voucher_descrip'] = 'Stock Transfer In Branch A/c against inv#'.$stock->stock_code;
                            $data['voucher_debit'] = abs($stock_total_net_amount);
                            $data['voucher_credit'] = 0;
                        break;
                        case "str":
                            $data['voucher_sr_no'] = 2;
                            $data['voucher_descrip'] = 'Stock Receive In Branch A/c  against inv#'.$stock->stock_code;
                            $data['voucher_debit'] = 0;
                            $data['voucher_credit'] = abs($stock_total_net_amount);
                        break;
                    }
                    // for credit entry vat_amount_total
                    $this->proAccoVoucherInsert($voucher_id,$action,$table_name,$data,$where_clause);
                }

                $allChartAcc = TblAccCoa::whereIn('chart_code',['6-01-10-0001'])->select('chart_account_id','chart_code','chart_name')->get();

                foreach ($allChartAcc as $oneChartAcc){
                    if($oneChartAcc->chart_code == '6-01-10-0001'){
                        $stock_vat_account = $oneChartAcc->chart_account_id;
                    }
                }
                if($request->overall_vat_amount > 0)
                {
                    $action = 'add';
                    $data['chart_account_id'] = $stock_vat_account;
                    switch ($formType) {
                        case "st":
                            $data['voucher_sr_no'] = 4;
                            $data['voucher_descrip'] = 'Adv Tax Amount against inv#'.$stock->stock_code;
                            $data['voucher_debit'] = 0;
                            $data['voucher_credit'] = abs($request->overall_vat_amount);
                        break;
                        case "str":
                            $data['voucher_sr_no'] = 3;
                            $data['voucher_descrip'] = 'Adv Tax Amount against inv#'.$stock->stock_code;
                            $data['voucher_debit'] = abs($request->overall_vat_amount);
                            $data['voucher_credit'] = 0;
                        break;
                    }
                    // for credit entry vat_amount_total
                    $this->proAccoVoucherInsert($voucher_id,$action,$table_name,$data,$where_clause);
                }

                if(!isset($id) || empty($stock->voucher_id)){
                    $stock_vochId = TblInveStock::where('stock_id',$stock_id)->first();
                    $stock_vochId->voucher_id = $voucher_id;
                    $stock_vochId->save();
                }
                // end insert update stock transfer voucher
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
            return $this->jsonErrorResponse($errData, $e->getMessage(), 200);
        }
        DB::commit();
        if(isset($id)){
            $data = array_merge($data, Utilities::returnJsonEditForm());
            $data['redirect'] = $this->prefixIndexPage.'stock/'.$type;
            return $this->jsonSuccessResponse($data, trans('message.update'), 200);
        }else{
            $data = array_merge($data, Utilities::returnJsonNewForm());
            $data['redirect'] = '/stock/'.$type.$this->prefixCreatePage.'/'.$form_id;
            return $this->jsonSuccessResponse($data, trans('message.create'), 200);
        }
    }

    public function getLocationByStore(Request $request)
    {
        $store_id  = $request->store_id;
        $data = [];
        $data['locations'] = ViewInveDisplayLocation::where('branch_id',auth()->user()->branch_id)->where('store_id',$store_id)->orderBy('display_location_name_string')->get();

        if(!empty($data['locations'])){
            $data['status'] = 'success';
        }else{
            $data['status'] = 'error';
        }
        return response()->json($data);
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

    public function print(Request $request, $type,$id)
    {
        switch ($type){
            case 'opening-stock': {
                $data['title'] = 'Opening Stock';
                $data['stock_code_type'] = 'os';
                $data['stock_menu_id'] = 54;
                break;
            }
            case 'stock-transfer': {
                $data['title'] = 'Stock Transfer';
                $data['stock_code_type'] = 'st';
                $data['stock_menu_id'] = 65;
                $data['type'] = $request->print;
                $url = '/stock/stock-transfer/print/'.$id;
                $data['stock_transfer_link'] = $url;
                break;
            }
            case 'stock-adjustment': {
                $data['title'] = 'Stock Adjustment';
                $data['stock_code_type'] = 'sa';
                $data['stock_menu_id'] = 55;
                break;
            }

            case 'stock-audit-adjustment': {
                $data['title'] = 'Stock Audit Adjustment';
                $data['stock_code_type'] = 'sa';
                $data['stock_menu_id'] = 312;
                $data['type'] = '';
                break;
            }
            case 'damaged-items': {
                $data['title'] = 'Damaged Items';
                $data['stock_code_type'] = 'di';
                $data['stock_menu_id'] = 57;
                break;
            }
            case 'expired-items': {
                $data['title'] = 'Expired Items';
                $data['stock_code_type'] = 'ei';
                $data['stock_menu_id'] = 58;
                break;
            }
            case 'sample-items': {
                $data['title'] = 'Sample Items';
                $data['stock_code_type'] = 'sp';
                $data['stock_menu_id'] = 56;
                break;
            }
            case 'repair-items': {
                $data['title'] = 'Repair Items';
                $data['stock_code_type'] = 'ri';
                $data['stock_menu_id'] = 57;
                break;
            }
            case 'stock-receiving': {
                $data['title'] = 'Stock Receiving';
                $data['stock_code_type'] = 'str';
                $data['stock_menu_id'] = 76;
                $data['type'] = $request->print;
                $url = '/stock/stock-receiving/print/'.$id;
                $data['print_link'] = $url;
                break;
            }
            case 'internal-stock-transfer': {
                $data['title'] = 'Internal Stock Transfer';
                $data['stock_code_type'] = 'ist';
                $data['stock_menu_id'] = '131';
                break;
            }
            case 'disassemble-products': {
                $data['title'] = 'Disassemble Product';
                $data['stock_code_type'] = 'dss';
                $data['stock_menu_id'] = '134';
                break;
            }
        }

        if(isset($id)){
            if(TblInveStock::where('stock_id','LIKE',$id)->exists()){
                $data['current'] = TblInveStock::with('stock_dtls','product','barcode')->where('stock_id',$id)->where('stock_code_type',$data['stock_code_type'])->first();
                if(!empty($data['current']->supplier_id) && isset($data['current']->supplier_id)){
                    $data['supplier'] = TblPurcSupplier::where('supplier_id',$data['current']->supplier_id)->select('supplier_id','supplier_name')->first();
                }
                $data['permission'] = $data['stock_menu_id'].'-print';
            }else{
                abort('404');
            }
        }
        $data['store_from'] = TblDefiStore::where('store_id',$data['current']->stock_store_from_id)->first();
        $data['store_to'] = TblDefiStore::where('store_id',$data['current']->stock_store_to_id)->first();
        $data['branch_from'] = TblSoftBranch::where('branch_id',$data['current']->stock_branch_from_id)->first();
        $data['branch_to'] = TblSoftBranch::where('branch_id',$data['current']->stock_branch_to_id)->first();
        $data['display_location'] = ViewInveDisplayLocation::where('branch_id',auth()->user()->branch_id)->where('store_id',$data['current']->stock_store_from_id)->first();
        $data['rate_by'] = config('constants.rate_by');

        if($type == 'stock-audit-adjustment'){
            return view('prints.inventory.stock_adjustment.audit_stock_adjustment', compact('data'));
        }

        if ($data['type'] == '1' && $type == 'stock-transfer') {
            return view('prints.inventory.stock_transfer.stock_transfer_print_landscape', compact('data'));
        }
        else if($data['type'] == '0' || $data['type'] == '' && $type == 'stock-transfer'){
            return view('prints.inventory.stock_transfer.stock_transfer_print',compact('data'));
        }
        else if($data['type'] == '4' && $type == 'stock-transfer'){
            return view('prints.inventory.stock_transfer.dispatch_print',compact('data'));
        }
        else if($data['type'] == '2' || $data['type'] == '' && $type == 'stock-receiving'){
            return view('prints.inventory.stock_recieve.stock_recieve_print',compact('data'));
        }else if($data['type'] == '3' && $type == 'stock-receiving'){
            return view('prints.inventory.stock_recieve.stock_recieve_landscape_print',compact('data'));
        }
    }

    public function fromStockPrint(Request $request, $type,$id) {
        switch ($type){
            case 'opening-stock': {
                $data['title'] = 'Opening Stock';
                $data['stock_code_type'] = 'os';
                $data['stock_menu_id'] = 54;
                break;
            }
            case 'stock-transfer': {
                $data['title'] = 'Stock Transfer';
                $data['stock_code_type'] = 'st';
                $data['stock_menu_id'] = 65;
                $data['type'] = $request->print;
                $url = '/stock/stock-transfer/from-stock-print/'.$id;
                $data['stock_transfer_link'] = $url;
                break;
            }
            case 'stock-adjustment': {
                $data['title'] = 'Stock Adjustment';
                $data['stock_code_type'] = 'sa';
                $data['stock_menu_id'] = 55;
                break;
            }
            case 'damaged-items': {
                $data['title'] = 'Damaged Items';
                $data['stock_code_type'] = 'di';
                $data['stock_menu_id'] = 57;
                break;
            }
            case 'expired-items': {
                $data['title'] = 'Expired Items';
                $data['stock_code_type'] = 'ei';
                $data['stock_menu_id'] = 58;
                break;
            }
            case 'sample-items': {
                $data['title'] = 'Sample Items';
                $data['stock_code_type'] = 'sp';
                $data['stock_menu_id'] = 56;
                break;
            }
            case 'repair-items': {
                $data['title'] = 'Repair Items';
                $data['stock_code_type'] = 'ri';
                $data['stock_menu_id'] = 57;
                break;
            }
            case 'stock-receiving': {
                $data['title'] = 'Stock Receiving';
                $data['stock_code_type'] = 'str';
                $data['stock_menu_id'] = 76;
                break;
            }
            case 'internal-stock-transfer': {
                $data['title'] = 'Internal Stock Transfer';
                $data['stock_code_type'] = 'ist';
                $data['stock_menu_id'] = '131';
                break;
            }
            case 'disassemble-products': {
                $data['title'] = 'Disassemble Product';
                $data['stock_code_type'] = 'dss';
                $data['stock_menu_id'] = '134';
                break;
            }
        }
        if(isset($id)){
            if(TblInveStock::where('stock_id','LIKE',$id)->where('stock_branch_to_id',auth()->user()->branch_id)->exists()){
                $data['current'] = TblInveStock::with('stock_dtls','product','barcode')->where('stock_id',$id)->where('stock_code_type',$data['stock_code_type'])->first();
                if(!empty($data['current']->supplier_id) && isset($data['current']->supplier_id)){
                    $data['supplier'] = TblPurcSupplier::where('supplier_id',$data['current']->supplier_id)->select('supplier_id','supplier_name')->first();
                }
            }else{
                abort('404');
            }
        }else{
            abort('404');
        }
        $data['store_from'] = TblDefiStore::where('store_id',$data['current']->stock_store_from_id)->first();
        $data['store_to'] = TblDefiStore::where('store_id',$data['current']->stock_store_to_id)->first();
        $data['branch_from'] = TblSoftBranch::where('branch_id',$data['current']->stock_branch_from_id)->first();
        $data['branch_to'] = TblSoftBranch::where('branch_id',$data['current']->stock_branch_to_id)->first();
        $data['display_location'] = ViewInveDisplayLocation::where('branch_id',auth()->user()->branch_id)->where('store_id',$data['current']->stock_store_from_id)->first();
        $data['rate_by'] = config('constants.rate_by');
        if ($type == 'stock-transfer') {
            return view('prints.inventory.stock_recieve.stock_recieve_from_print',compact('data'));
        }else{
            return view('prints.stock_print_from',compact('data'));
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getStockRequestDtlData(Request $request)
    {
        $data = [];

        DB::beginTransaction();
        try{
            $rate_type = $request->rate_type;
            $rate_perc = $request->rate_perc;
            $data['stock'] = TblPurcDemand::with('dtls')->where('demand_id',$request->stock_id)->first();

            $data['rate'] = [];

            foreach ($data['stock']->dtls as $k=>$dtl){
                $rate = TblPurcProductBarcodeSaleRate::where('product_barcode_id',$dtl['product_barcode_id'])->where('branch_id',auth()->user()->branch_id)->where('product_category_id',2)->first();
                $data['rate'][$k]['rate'] = $rate['product_barcode_sale_rate_rate'];

                $purc_rate = TblPurcProductBarcodePurchRate::where('product_barcode_barcode', $dtl['product_barcode_barcode'])
                ->where('product_barcode_id',$dtl['product_barcode_id'])
                ->where('branch_id',auth()->user()->branch_id)->first();
                $pur_rate = 0;
                if($rate_type == 'item_cost_rate'){
                    $pur_rate = $purc_rate['product_barcode_cost_rate'];
                }
                if($rate_type == 'item_sale_rate'){
                    $pur_rate = $rate['product_barcode_sale_rate_rate'];
                }
                if($rate_type == 'item_average_rate'){
                    $pur_rate = $purc_rate['product_barcode_avg_rate'];
                }
                if($rate_type == 'item_purchase_rate'){
                    $pur_rate = $purc_rate['product_barcode_purchase_rate'];
                }
                $data['rate'][$k]['purc_rate'] = $pur_rate;


                $vat = TblPurcProductBarcodeDtl::where('product_barcode_id',$dtl['product_barcode_id'])
                ->where('branch_id',auth()->user()->branch_id)->first();
                $vat_purc = 0;
                if(!empty($vat) && $vat->product_barcode_tax_apply == 1){
                    $vat_purc = $vat->product_barcode_tax_value;
                }
                $data['rate'][$k]['vat_purc'] = $vat_purc;
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
        return $this->jsonSuccessResponse($data, 'Stock Request Data loaded', 200);
    }


// Get farmulation Data
    public function getFormulationRequestData(Request $request)
    {
        $data = [];
        $validator = Validator::make($request->all(), [
            'stock_id' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            $data['validator_errors'] = $validator->errors();
            return $this->jsonErrorResponse($data, 'Please Select Formulation Code First', 422);
        }

        $id = $request->stock_id;
        DB::beginTransaction();
        try{

            //$data['current'] = TblInveItemFormulation::with('product','dtls')->where('item_formulation_id',$id)->first();
            $data['formulation'] = TblInveItemFormulation::with('product','rate','dtls')
            ->where(Utilities::currentBCB())->where('item_formulation_id',$id)->first();
            dd($data['formulation']->toArray());
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
        return $this->jsonSuccessResponse($data, 'Formulation Data loaded', 200);

    }


    public function getStockTransferDtlData(Request $request)
    {
        $data = [];

        DB::beginTransaction();
        try{

            $data['stock'] = TblInveStock::with('stock_dtls')->where('stock_id',$request->stock_id)->first();
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
        return $this->jsonSuccessResponse($data, 'Stock Transfer Data loaded', 200);
    }

    public function getGRNDtlData(Request $request)
    {
        $data = [];

        DB::beginTransaction();
        try{

            $data['grn'] = TblPurcGrn::with('grn_dtl')->where('grn_id',$request->grn_id)->first();

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
        return $this->jsonSuccessResponse($data, 'GRN Data loaded', 200);
    }




    public function destroy($type,$id)
    {
        $data = [];
        DB::beginTransaction();
        try{
            $stock= TblInveStock::where('stock_id',$id)->first();
            $st_id = $stock->stock_request_id;

            if(!empty($st_id)){
                TblInveStock::where('stock_id',$st_id)->update(['stock_receive_status'=>'0']);
            }

            $stock->stock_dtls()->delete();
            TblAccoVoucher::where('voucher_document_id',$id)->where(Utilities::currentBCB())->delete();
            $stock->delete();

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

    public function importFile($type){
        $data = [];
        $data['page_data'] = [];
        $data['form_type'] = $type;
        $data['page_data'] = array_merge($data['page_data'], Utilities::newForm());
        $data['page_data']['action'] = 'Import';
        $data['page_data']['type'] = 'Import';
        $data['page_data']['path_index'] = $this->prefixIndexPage.'stock/'.$type;
        $doc_data = [
            'biz_type'          => 'branch',
            'model'             => 'TblInveStock',
            'code_field'        => 'stock_code',
            'code_prefix'       => strtoupper('os'),
            'code_type_field'   => 'stock_code_type',
            'code_type'         => 'os'
        ];
        $data['stock_code'] = Utilities::documentCode($doc_data);
        $data['store'] = TblDefiStore::get();
        return view('inventory.opening_stock.import',compact('data'));
    }

    public function importExcle(Request $request){
        $data = [];
        $validator = Validator::make($request->all(), [
            'store' => 'required|not_in:0',
            'file' => 'required|mimes:xlsx,xls,csv'
        ]);
        if ($validator->fails()) {
            $data['validator_errors'] = $validator->errors();
            return $this->jsonErrorResponse($data, trans('message.required_fields'), 422);
        }

        DB::beginTransaction();
        try {
            $file = $request->file('file');
            $path = public_path('/upload/');
            $filename = time() . '-' . $file->getClientOriginalName();
            $fileExtension = time() . '-' . $file->getClientOriginalExtension();
            $file->move($path,$filename);

            $excel = Importer::make('Excel');
            $excel->load($path.$filename);
            $collection = $excel->getCollection();
           // dd($collection);
            $formType = $request->form_type; // opening Stock
            $now = new \DateTime("now");
            $today_format = $now->format("d-m-Y");

            $endEntry = 200;
            $count = round(count($collection) / $endEntry);
            if(($count*200) < count($collection)){
                $count += 1;
            }
            $totalSuccess = 0;
            $totalFail = 0;
            $doc_data = [
                'biz_type'          => 'branch',
                'model'             => 'TblInveStock',
                'code_field'        => 'stock_code',
                'code_prefix'       => strtoupper($formType),
                'code_type_field'   => 'stock_code_type',
                'code_type'         => $formType

            ];
            for($i=1; $count >= $i; $i++){
                $stock_total_qty = 0;
                $stock_total_amount = 0;
                $stock = new TblInveStock();
                $stock->stock_id =  Utilities::uuid();
                $stock->stock_code = Utilities::documentCode($doc_data);
                $stock->stock_code_type =  $formType;
                $stock->stock_date =   date('Y-m-d', strtotime($today_format));
                $stock->stock_menu_id =  54;
                $stock->stock_store_from_id = $request->store;
                $stock->stock_entry_status = 1;
                $stock->business_id = auth()->user()->business_id;
                $stock->company_id = auth()->user()->company_id;
                $stock->branch_id = auth()->user()->branch_id;
                $stock->stock_user_id = auth()->user()->id;
                $stock->save();
                for ($row=(($endEntry*$i)-199); $row <= ($endEntry*$i); $row++){
                    $barcode = TblPurcProductBarcode::where('product_barcode_barcode','like',$collection[$row][6])->first();
                    if(!empty($barcode)){
                        $dtl = new TblInveStockDtl();
                        $dtl->stock_id = (int)$stock->stock_id;
                        $dtl->stock_dtl_id = Utilities::uuid();
                        $dtl->stock_dtl_sr_no = $row;
                        $qty = isset($collection[$row][2])?$this->addNo($collection[$row][2]):0;
                        $amount = isset($collection[$row][5])?$this->addNo($collection[$row][5]):0;
                        $dtl->stock_dtl_quantity =  $qty;
                        $dtl->stock_dtl_rate =  $collection[$row][4];
                        $dtl->stock_dtl_amount =  $collection[$row][5];
                        $dtl->product_barcode_barcode =  $collection[$row][6];
                        $dtl->product_id =  (int)$barcode->product_id;
                        $dtl->product_barcode_id =  (int)$barcode->product_barcode_id;
                        $dtl->uom_id =  (int)$barcode->uom_id;
                        $dtl->stock_dtl_packing =  $barcode->product_barcode_packing;
                        $packing = isset($barcode->product_barcode_packing)?$this->addNo($barcode->product_barcode_packing):0;
                        $dtl->stock_dtl_qty_base_unit =  $qty;
                        $dtl->business_id = auth()->user()->business_id;
                        $dtl->company_id = auth()->user()->company_id;
                        $dtl->branch_id = auth()->user()->branch_id;
                        $dtl->dtl_user_id = auth()->user()->id;

                        $stock_total_qty += $qty;
                        $stock_total_amount += $amount;
                        $dtl->save();
                        $totalSuccess++;
                        if($row == (count($collection)-1)){
                            break;
                        }
                    }else{
                        $totalFail++;
                    }
                }
                $stock = TblInveStock::where('stock_id',$stock->stock_id)->first();
                $stock->stock_total_qty =  $stock_total_qty;
                $stock->stock_total_amount =  $stock_total_amount;
                $stock->save();
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
        $data = array_merge($data, Utilities::returnJsonImportForm());
        $message = 'Total Fail:'.$totalFail.'<br/> Total Success:'.$totalSuccess;
        return $this->jsonSuccessResponse($data, $message, 200);

    }

    public function changeProductGroup (Request $request){
        dd('OK');
    }
}
