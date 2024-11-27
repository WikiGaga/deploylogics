<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Library\Utilities;
use App\Models\TblAccoVoucher;
use App\Models\TblDefiConfigBranches;
use App\Models\TblDefiPaymentType;
use App\Models\TblDefiStore;
use App\Models\TblInveDeal;
use App\Models\TblInveDealDtl;
use App\Models\TblInveItemFormulation;
use App\Models\TblPurcDemand;
use App\Models\TblPurcDemandDtl;
use App\Models\TblPurcGrn;
use App\Models\TblPurcProductBarcode;
use App\Models\TblPurcProductBarcodeDtl;
use App\Models\TblPurcProductBarcodePurchRate;
use App\Models\TblPurcProductBarcodeSaleRate;
use App\Models\TblPurcSupplier;
use App\Models\TblSoftBranch;
use App\Models\ViewInveDisplayLocation;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Importer;
use Illuminate\Validation\ValidationException;

class DealSetupController extends Controller
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

    public function create($id = null)
    {
        $locale = Session::get('locale');
        //dd($locale);
        $data['page_data'] = [];

        $data['page_data']['title'] = 'Deal Setup';
        $formUrl = 'deal_setup';
        $data['stock_code_type'] = 'ds';
        $data['stock_menu_id'] = '133';

        // dd($type);
        $data['form_type'] = 'deal-setup';
        $data['page_data']['path_index'] = $this->prefixIndexPage.'deal-setup';
        $data['page_data']['create'] = '/'.'deal-setup'.$this->prefixCreatePage;
        if(isset($id)){
            if(TblInveDeal::where('stock_id','LIKE',$id)->where(Utilities::currentBCB())->exists()){
                $data['page_data'] = array_merge($data['page_data'], Utilities::editForm());
                $data['permission'] = $data['stock_menu_id'].'-edit';
                $data['id'] = $id;
                $data['current'] = TblInveDeal::with('stock_dtls','product','barcode','supplier','formulation')->where(Utilities::currentBCB())->where('stock_id',$id)->where('stock_code_type',$data['stock_code_type'])->first();

               // dd($data['current']->toArray());
                $data['display_location'] = ViewInveDisplayLocation::where('branch_id',auth()->user()->branch_id)->where('store_id',$data['current']->stock_store_from_id)->orderBy('display_location_name_string')->get();
                $data['page_data']['print'] = '/'.'deal-setup'.'/print/'.$id;
                $data['stock_code'] = $data['current']->stock_code;
            }else{
                abort('404');
            }
        }else{
            $data['permission'] = $data['stock_menu_id'].'-create';
            $data['page_data'] = array_merge($data['page_data'], Utilities::newForm());
            $doc_data = [
                'biz_type'          => 'branch',
                'model'             => 'TblInveDeal',
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
            'table_name' => 'tbl_inve_deal',
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
    public function store(Request $request, $id = null)
    {
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
            if(isset($id)){
                $stock = TblInveDeal::where('stock_id',$id)->first();
            }else{
                $stock = new TblInveDeal();
                $stock->stock_id =  Utilities::uuid();
                $doc_data = [
                    'biz_type'          => 'branch',
                    'model'             => 'TblInveDeal',
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
            $stock->start_date =  date('Y-m-d H:i',strtotime($request->start_date));;
            $stock->end_date =  ($request->end_date =='01-01-1970' || $request->end_date =='')?'':date('Y-m-d H:i',strtotime($request->end_date));
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

            $stock->stock_store_from_id =  isset($request->store)?$request->store:"";
            $stock->stock_store_to_id =  isset($request->store_to)?$request->store_to:"";
            $stock->stock_branch_from_id =  isset($request->branch)?$request->branch:"";
            $stock->stock_branch_to_id =  isset($request->branch_to)?$request->branch_to:"";
            $stock->sales_sales_type =  isset($request->sales_sales_type)?$request->sales_sales_type:"";
            $stock->stock_rate_by =  isset($request->selected_barcode_rate)?$request->selected_barcode_rate:"";
            $stock->stock_remarks = $request->stock_remarks;
            $stock->stock_request_id = isset($request->stock_from_id)?$request->stock_from_id:'';
            $stock->stock_location_id = isset($request->stock_location_id)?$request->stock_location_id:'';
            $stock->stock_entry_status = 1;
            $stock->supplier_id = isset($request->supplier_id)?$request->supplier_id:'';
            $stock->business_id = auth()->user()->business_id;
            $stock->company_id = auth()->user()->company_id;
            $stock->branch_id = auth()->user()->branch_id;
            $stock->stock_user_id = auth()->user()->id;
            $stock->save();

            $del_Dtls = TblInveDealDtl::where('stock_id',$id)->get();
            foreach ($del_Dtls as $del_Dtl){
                TblInveDealDtl::where('stock_dtl_id',$del_Dtl->stock_dtl_id)->delete();
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
                    $dtl = new TblInveDealDtl();
                    $dtl->stock_id =  $stock->stock_id;
                    $dtl->stock_dtl_id =  Utilities::uuid();
                    $dtl->stock_dtl_sr_no =  $key++;
                    $dtl->product_id =  $pd['product_id'];
                    $dtl->product_barcode_id =  $pd['product_barcode_id'];
                    $dtl->product_barcode_barcode =  $pd['pd_barcode'];
                    $dtl->uom_id =  $pd['uom_id'];
                    $dtl->stock_dtl_packing = isset($pd['pd_packing'])?$pd['pd_packing']:'';
                    $dtl->stock_dtl_quantity =  $pd['quantity'];
                    $dtl->stock_dtl_qty_base_unit =  $pd['quantity'] ;
                    $dtl->stock_dtl_rate =  isset($pd['rate'])?$this->addNo($pd['rate']):"";
                    $dtl->stock_dtl_sale_rate =  isset($pd['sale_rate'])?$this->addNo($pd['sale_rate']):"";
                    $dtl->stock_dtl_sale_amount =  isset($pd['sale_amount'])?$this->addNo($pd['sale_amount']):"";
                    $dtl->cost_rate =  isset($pd['cost_rate'])?$this->addNo($pd['cost_rate']):"";
                    $dtl->cost_amount =  isset($pd['cost_amount'])?$this->addNo($pd['cost_amount']):"";
                    $dtl->stock_dtl_purc_rate =  isset($pd['purc_rate'])?$this->addNo($pd['purc_rate']):"";
                    $dtl->stock_dtl_demand_quantity =  isset($pd['demand_qty'])?$pd['demand_qty']:"";
                    $dtl->stock_dtl_stock_transfer_qty =  isset($pd['stock_transfer_qty'])?$pd['stock_transfer_qty']:"";
                    //add new column formula qty
                    $dtl->stock_dtl_formula_qty = isset($pd['formula_qty']) ? $pd['formula_qty'] : 0;
                    $dtl->stock_dtl_batch_no =  isset($pd['batch_no'])?$pd['batch_no']:"";
                    $dtl->mrp =  isset($pd['mrp'])?$pd['mrp']:"";
                    $dtl->stock_dtl_store =  isset($pd['pd_store'])?$pd['pd_store']:"";
                    $dtl->stock_dtl_amount =  isset($pd['amount'])?$this->addNo($pd['amount']):"";
                    $dtl->stock_dtl_disc_percent =  isset($pd['dis_perc'])?$this->addNo($pd['dis_perc']):"";
                    $dtl->stock_dtl_disc_amount =  isset($pd['dis_amount'])?$this->addNo($pd['dis_amount']):"";
                    $dtl->stock_dtl_vat_percent =  isset($pd['vat_perc'])?$this->addNo($pd['vat_perc']):"";
                    $dtl->stock_dtl_vat_amount =  isset($pd['vat_amount'])?$this->addNo($pd['vat_amount']):"";
                    $dtl->stock_dtl_total_amount =  isset($pd['gross_amount'])?$this->addNo($pd['gross_amount']):"";
                    $dtl->stock_dtl_stock_quantity =  isset($pd['stock_quantity'])?$pd['stock_quantity']:"";
                    $dtl->stock_dtl_physical_quantity =  isset($pd['physical_quantity'])?$pd['physical_quantity']:"";
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
                    $dtl->save();
                }
            }
            $stock = TblInveDeal::where('stock_id',$stock->stock_id)->first();
            $stock->stock_total_qty =  $stock_total_qty;
            $stock->stock_total_amount =  $stock_total_amount;
            $stock->save();

           // dd($formType);
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
            $data['redirect'] = $this->prefixIndexPage.'deal-setup';
            return $this->jsonSuccessResponse($data, trans('message.update'), 200);
        }else{
            $data = array_merge($data, Utilities::returnJsonNewForm());
            $data['redirect'] = '/'.'deal-setup'.$this->prefixCreatePage.'/'.$form_id;
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

    // public function print(Request $request, $type,$id)
    // {
    //     switch ($type){
    //         case 'deal-setup': {
    //             $data['page_data']['title'] = 'Deal Setup';
    //             $formUrl = 'deal_setup';
    //             $data['stock_code_type'] = 'ds';
    //             $data['stock_menu_id'] = '133';
    //             break;
    //         }
    //     }
    //     if(isset($id)){
    //         if(TblInveDeal::where('stock_id','LIKE',$id)->exists()){
    //             $data['current'] = TblInveDeal::with('stock_dtls','product','barcode')->where('stock_id',$id)->where('stock_code_type',$data['stock_code_type'])->first();
    //             if(!empty($data['current']->supplier_id) && isset($data['current']->supplier_id)){
    //                 $data['supplier'] = TblPurcSupplier::where('supplier_id',$data['current']->supplier_id)->select('supplier_id','supplier_name')->first();
    //             }
    //             $data['permission'] = $data['stock_menu_id'].'-print';
    //         }else{
    //             abort('404');
    //         }
    //     }
    //     $data['store_from'] = TblDefiStore::where('store_id',$data['current']->stock_store_from_id)->first();
    //     $data['store_to'] = TblDefiStore::where('store_id',$data['current']->stock_store_to_id)->first();
    //     $data['branch_from'] = TblSoftBranch::where('branch_id',$data['current']->stock_branch_from_id)->first();
    //     $data['branch_to'] = TblSoftBranch::where('branch_id',$data['current']->stock_branch_to_id)->first();
    //     $data['display_location'] = ViewInveDisplayLocation::where('branch_id',auth()->user()->branch_id)->where('store_id',$data['current']->stock_store_from_id)->first();
    //     $data['rate_by'] = config('constants.rate_by');
    //     if ($data['type'] == '1' && $type == 'stock-transfer') {
    //         return view('prints.inventory.stock_transfer.stock_transfer_print_landscape', compact('data'));
    //     }
    //     else if($data['type'] == '0' || $data['type'] == '' && $type == 'stock-transfer'){
    //         return view('prints.inventory.stock_transfer.stock_transfer_print',compact('data'));
    //     }
    //     else if($data['type'] == '2' || $data['type'] == '' && $type == 'stock-receiving'){
    //         return view('prints.inventory.stock_recieve.stock_recieve_print',compact('data'));
    //     }else if($data['type'] == '3' && $type == 'stock-receiving'){
    //         return view('prints.inventory.stock_recieve.stock_recieve_landscape_print',compact('data'));
    //     }
    // }

    public function fromStockPrint(Request $request, $type,$id) {
        switch ($type){
            case 'deal-setup': {
                $data['page_data']['title'] = 'Deal Setup';
                $formUrl = 'deal_setup';
                $data['stock_code_type'] = 'ds';
                $data['stock_menu_id'] = '133';
                break;
            }
        }
        if(isset($id)){
            if(TblInveDeal::where('stock_id','LIKE',$id)->where('stock_branch_to_id',auth()->user()->branch_id)->exists()){
                $data['current'] = TblInveDeal::with('stock_dtls','product','barcode')->where('stock_id',$id)->where('stock_code_type',$data['stock_code_type'])->first();
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

            $data['stock'] = TblInveDeal::with('stock_dtls')->where('stock_id',$request->stock_id)->first();

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
            $stock= TblInveDeal::where('stock_id',$id)->first();
            $stock->stock_dtls()->delete();
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
        $data['page_data']['path_index'] = $this->prefixIndexPage.$type;
        $doc_data = [
            'biz_type'          => 'branch',
            'model'             => 'TblInveDeal',
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
                'model'             => 'TblInveDeal',
                'code_field'        => 'stock_code',
                'code_prefix'       => strtoupper($formType),
                'code_type_field'   => 'stock_code_type',
                'code_type'         => $formType

            ];
            for($i=1; $count >= $i; $i++){
                $stock_total_qty = 0;
                $stock_total_amount = 0;
                $stock = new TblInveDeal();
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
                        $dtl = new TblInveDealDtl();
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
                $stock = TblInveDeal::where('stock_id',$stock->stock_id)->first();
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
