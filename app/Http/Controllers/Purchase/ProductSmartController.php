<?php

namespace App\Http\Controllers\Purchase;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Library\Utilities;
use App\Models\Defi\TblDefiConstants;
use App\Models\TblPurcProduct;
use App\Models\Defi\TblDefiTaxGroup;
use App\Models\Sale\TblSaleDiscountSetup;
use App\Models\Sale\TblSaleDiscountSetupMembership;
use App\Models\TblDefiGSTCalculation;
use App\Models\TblDefiMembershipType;
use App\Models\TblInveStockDtl;
use App\Models\TblPurcGrnDtl;
use App\Models\TblPurcGroupItem;
use App\Models\TblPurcProductBarcode;
use App\Models\TblPurcProductBarcodeDtl;
use App\Models\TblPurcProductBarcodePurchRate;
use App\Models\TblPurcProductFOC;
use App\Models\TblPurcPurchaseOrderDtl;
use App\Models\TblPurcSupplier;
use App\Models\TblSaleSalesDtl;
use App\Models\TblSchemeBranches;
use App\Models\TblSoftBranch;
use App\Models\TblDefiStore;
use App\Models\ViewPurcGroupItem;
use App\Models\ViewPurcPoGrnStock;
use App\Models\ViewPurcProductBarcode;
use App\Models\ViewPurcProductBarcodeRate;
use App\Models\ViewPurcStockDtl;
use App\Models\User;
use Illuminate\Http\Request;
use Image;
use Browser;
use Importer;

// db and Validator
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Support\Facades\Session;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Validator;

class ProductSmartController extends Controller
{
    public static $page_title = 'Product';
    public static $redirect_url = 'product';
    public static $menu_dtl_id = '282';

    public function __construct()
    {
        $getStaticPrefix = Utilities::getStaticPrefix(self::$redirect_url);
        $this->current_path = $getStaticPrefix['path'];
        $this->page_form = '/'.self::$redirect_url.'/form';
        $this->page_view = '/'.self::$redirect_url.'/view';
    }
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
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request,$id = null){

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

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function destroy($id)
    {
        //
    }

    /**
     * Alternate Barcode
     *
     */
    public function viewAlternateBarcode(){
        $data = [];
        $data['page_data']['title'] = 'Alternate Barcode';
        $data['page_data']['path_index'] = '';
        $data['page_data']['create'] = '';

        return view('purchase.product_smart.alternate_barcode', compact('data'));
    }

    public function getAlternateBarcodeProduct(Request $request){
        $data = [];
        $barcode = isset($request->barcode)?$request->barcode:"";
        if(!empty($barcode)){
            $branch_id = auth()->user()->branch_id;
            $q = "select pb.PRODUCT_ID from TBL_PURC_PRODUCT_BARCODE pb where pb.PRODUCT_BARCODE_BARCODE like '$barcode'";
            $prod = DB::selectOne($q);

            if(isset($prod->product_id)){

                $qry = "select PRODUCT_ID,PRODUCT_NAME,PRODUCT_BARCODE_ID,PRODUCT_BARCODE_BARCODE,uom_name,product_barcode_packing,SALE_RATE,PRODUCT_BARCODE_COST_RATE,BASE_BARCODE
                from VW_PURC_PRODUCT_BARCODE_RATE pb where pb.PRODUCT_ID =".$prod->product_id." and branch_id = ".auth()->user()->branch_id;
                $data['list'] = DB::select($qry);
                $data['current'] = [];
                foreach ($data['list'] as $key => $value) {
                    if ($value->base_barcode ==1 ) {
                        $data['current'] = $value;
                        unset($data['list'][$key]);
                    }
                }
                $data['list'] = array_values($data['list']);

                if(empty($data)){
                    $qry = "select p.PRODUCT_ID,p.PRODUCT_name,pb.PRODUCT_BARCODE_ID,pb.PRODUCT_BARCODE_BARCODE,uom.uom_name,pbpr.SALE_RATE,pbpr.PRODUCT_BARCODE_COST_RATE COST_RATE from TBL_PURC_PRODUCT_BARCODE pb
                    join TBL_PURC_PRODUCT p on p.product_id = pb.product_id
                    join TBL_DEFI_UOM uom on uom.uom_id = pb.uom_id
                    join TBL_PURC_PRODUCT_BARCODE_PURCH_RATE pbpr on (pbpr.PRODUCT_BARCODE_ID = pb.PRODUCT_BARCODE_ID)
                    where pb.PRODUCT_ID = ".$prod->product_id." and pb.branch_id = ".auth()->user()->branch_id;

                    $data = DB::selectOne($qry);
                }
            }
            if(empty($data)){
                return $this->jsonErrorResponse($data, "Barcode data not found", 200);
            }
        }else{
            return $this->jsonErrorResponse($data, "Barcode data not available", 200);
        }

        return $this->jsonSuccessResponse($data, 'loaded', 200);
    }

    public function storeAlternateBarcode(Request $request)
    {
        $data = [];
        $product_id = isset($request->product_id)?$request->product_id:"";
        $barcode_id = isset($request->barcode_id)?$request->barcode_id:"";
        $new_barcode = isset($request->barcode)?$request->barcode:"";
        $packing = isset($request->packing)?$request->packing:"";
        $dataNotFound = false;

        DB::beginTransaction();
        try{
            if(!empty($product_id) && !empty($barcode_id) && !empty($new_barcode) && !empty($packing)){
                $exists = TblPurcProductBarcode::where('product_barcode_id',$barcode_id)->exists();
                $alreadyExists = TblPurcProductBarcode::where('product_barcode_barcode',$new_barcode)->exists();
                //dd($exists);
                if($alreadyExists){
                    return $this->jsonErrorResponse($data, $new_barcode." - Barcode already exists.", 200);
                }
                $max = TblPurcProductBarcode::where('product_barcode_id',$barcode_id)->max('product_barcode_sr_no');
                if($exists){
                    $baseBarcode = TblPurcProductBarcode::where('product_id',$product_id)->where('base_barcode',1)->first();
                    if(empty($baseBarcode)){
                        return $this->jsonErrorResponse($data, "Base Barcode not found.", 200);
                    }
                    $product_barcode_id = Utilities::uuid();
                    $data['product_barcode_id'] = $product_barcode_id;
                    $barcode = new TblPurcProductBarcode();
                    $barcode->product_barcode_id = $product_barcode_id;
                    $v_product_barcode = trim($new_barcode);
                    $barcode->product_id = $product_id;
                    $barcode->product_barcode_barcode = $v_product_barcode;
                    $barcode->product_barcode_entry_status = 1;
                    $barcode->product_barcode_sr_no = $max + 1;
                    $barcode->uom_id = $baseBarcode->uom_id;
                    $barcode->product_barcode_packing = $packing;
                    $barcode->variant_id = $baseBarcode->variant_id;
                    $barcode->color_id = $baseBarcode->color_id;
                    $barcode->size_id = $baseBarcode->size_id;
                    $barcode->weight_id = $baseBarcode->weight_id;
                    $barcode->hs_code = $baseBarcode->hs_code;
                    $barcode->tax_group_id = $baseBarcode->tax_group_id;
                    $barcode->product_barcode_user_id = auth()->user()->id;
                    $barcode->business_id = auth()->user()->business_id;
                    $barcode->base_barcode = 0;
                    $barcode->save();

                    $purcRates = TblPurcProductBarcodePurchRate::where('product_barcode_id',$barcode_id)->get();
                    foreach ($purcRates as $purcRate){
                        TblPurcProductBarcodePurchRate::create([
                            'product_barcode_purch_id' => Utilities::uuid(),
                            'product_id' => $product_id,
                            'product_barcode_id' => $product_barcode_id,
                            'product_barcode_barcode' => $v_product_barcode,
                            'product_barcode_cost_rate' => $purcRate->product_barcode_cost_rate,
                            'sale_rate' => $purcRate->sale_rate,
                            'tax_rate' => $purcRate->tax_rate,
                            'inclusive_tax_price' => $purcRate->inclusive_tax_price,
                            'gp_perc' => $purcRate->gp_perc,
                            'gp_amount' => $purcRate->gp_amount,
                            'business_id' => $purcRate->business_id,
                            'company_id' => $purcRate->company_id,
                            'branch_id' => $purcRate->branch_id,
                        ]);
                    }

                    $BarcodeDtls = TblPurcProductBarcodeDtl::where('product_barcode_id',$barcode_id)->get();
                    foreach ($BarcodeDtls as $BarcodeDtl){
                        TblPurcProductBarcodeDtl::create([
                            'product_barcode_dtl_id' => Utilities::uuid(),
                            'product_barcode_id' => $product_barcode_id,
                            'product_barcode_shelf_stock_location' => $BarcodeDtl->product_barcode_shelf_stock_location,
                            'product_barcode_shelf_stock_sales_man' => $BarcodeDtl->product_barcode_shelf_stock_sales_man,
                            'product_barcode_shelf_stock_max_qty' => $BarcodeDtl->product_barcode_shelf_stock_max_qty,
                            'product_barcode_shelf_stock_min_qty' => $BarcodeDtl->product_barcode_shelf_stock_min_qty,
                            'product_barcode_stock_cons_day' => $BarcodeDtl->product_barcode_stock_cons_day,
                            'product_barcode_stock_limit_neg_stock' => $BarcodeDtl->product_barcode_stock_limit_neg_stock,
                            'product_barcode_stock_limit_limit_apply' => $BarcodeDtl->product_barcode_stock_limit_limit_apply,
                            'product_barcode_stock_limit_reorder_qty' => $BarcodeDtl->product_barcode_stock_limit_reorder_qty,
                            'product_barcode_stock_limit_status' => $BarcodeDtl->product_barcode_stock_limit_status,
                            'product_barcode_stock_limit_max_qty' => $BarcodeDtl->product_barcode_stock_limit_max_qty,
                            'product_barcode_stock_limit_min_qty' => $BarcodeDtl->product_barcode_stock_limit_min_qty,
                            'product_barcode_tax_value' => $BarcodeDtl->product_barcode_tax_value,
                            'product_barcode_tax_apply' => $BarcodeDtl->product_barcode_tax_apply,
                            'product_barcode_stock_limit_reorder_point' => $BarcodeDtl->product_barcode_stock_limit_reorder_point,
                            'product_barcode_shelf_stock_reorder_point' => $BarcodeDtl->product_barcode_shelf_stock_reorder_point,
                            'product_barcode_shelf_stock_dept_qty' => $BarcodeDtl->product_barcode_shelf_stock_dept_qty,
                            'product_barcode_shelf_stock_face_qty' => $BarcodeDtl->product_barcode_shelf_stock_face_qty,
                            'business_id' => $BarcodeDtl->business_id,
                            'company_id' => $BarcodeDtl->company_id,
                            'branch_id' => $BarcodeDtl->branch_id,
                        ]);
                    }
                    TblPurcProduct::where('product_id',$product_id)->update([
                        'update_id' => Utilities::uuid()
                    ]);
                }else{
                    $dataNotFound = true;
                }


                if($dataNotFound){
                    DB::rollback();
                    return $this->jsonErrorResponse($data, "Base Barcode not found", 200);
                }
            }else{
                DB::rollback();
                return $this->jsonErrorResponse($data, "Barcode is required", 200);
            }
        }catch (Exception $e){
            DB::rollback();
            return $this->jsonErrorResponse($data, $e->getLine().": ".$e->getMessage(), 200);
        }
        DB::commit();


        return $this->jsonSuccessResponse($data, 'Barcode successfully created', 200);
    }

    public function removeAlternateBarcode(Request $request){
        $data = [];
        $barcode_id = isset($request->barcode)?$request->barcode:"";
        $dataNotFound = false;
        $dataHasInve = false;
        DB::beginTransaction();
        try{
            if(!empty($barcode_id)){

                $po = TblPurcPurchaseOrderDtl::where('product_barcode_id',$barcode_id)->exists();
                $stock = ViewPurcStockDtl::where('product_barcode_id',$barcode_id)->exists();

                if(empty($po) && empty($stock) ) {

                    $exists = TblPurcProductBarcode::where('product_barcode_id',$barcode_id)->first();
                    $countBarcode = TblPurcProductBarcode::where('product_id',$exists->product_id)->count();
                    if($countBarcode <= 1){
                        return $this->jsonErrorResponse($data, "Minimum one barcode required with product.", 200);
                    }
                    if(!empty($exists)){

                        TblPurcProductBarcodePurchRate::where('product_barcode_id',$barcode_id)->delete();
                        TblPurcProductBarcodeDtl::where('product_barcode_id',$barcode_id)->delete();
                        TblPurcProductBarcode::where('product_barcode_id',$barcode_id)->delete();

                    }else{
                        $dataNotFound = true;
                    }

                }else{
                    $dataHasInve = true;
                }


                if($dataHasInve){
                    DB::rollback();
                    return $this->jsonErrorResponse($data, "Barcode used.", 200);
                }
                if($dataNotFound){
                    DB::rollback();
                    return $this->jsonErrorResponse($data, "Barcode not found", 200);
                }
            }else{
                DB::rollback();
                return $this->jsonErrorResponse($data, "Barcode is required", 200);
            }
        }catch (Exception $e){
            DB::rollback();
            return $this->jsonErrorResponse($data, "Barcode is required", 200);
        }
        DB::commit();


        return $this->jsonSuccessResponse($data, 'Barcode successfully removed', 200);
    }



    /**
     * Product Item Modal
     *
     */
    public function openModalProductFilter(Request $request){
        
        $data = [];
        $data['supplier'] = TblPurcSupplier::orderBy('supplier_name')->where(Utilities::currentBC())->get();
        $filterAjax = false;

        $product = ViewPurcProductBarcodeRate::where('product_id','<>',0);

        if(isset($request->form_type) && $request->form_type == 'item_tax') {
            $product = $product->select('product_id','product_name','uom_id','uom_name','product_barcode_packing','product_barcode_id','product_barcode_barcode',
                'product_barcode_cost_rate as cost_rate','sale_rate','mrp','group_item_id','group_item_name','supplier_id','supplier_name');
        }
        if(isset($request->form_type) && $request->form_type == 'change_rate') {
            /*$product = $product->select('product_id','product_name','uom_id','uom_name','product_barcode_packing','product_barcode_id','product_barcode_barcode',
                'product_barcode_cost_rate as cost_rate','sale_rate','mrp','group_item_id','group_item_name','supplier_id','supplier_name');
            */
            $product = $product->select('product_id','product_name','uom_id','uom_name','product_barcode_packing','product_barcode_id','product_barcode_barcode',
            'net_tp','sale_rate');
        }

        if(isset($request->form_type) && $request->form_type == 'shelf_stock') {
            $product = $product->select('product_id','product_name','product_barcode_id','product_barcode_barcode','hs_code','gst_calculation_id','gst_calculation_name','group_item_name','supplier_name',
                'product_barcode_cost_rate as cost_rate','sale_rate','mrp','tax_group_id','tax_group_name','product_type_group_name','brand_name');
        }

        if(isset($request->form_type) && $request->form_type == 'purchase_order') {
            $product = $product->select('product_id','product_name','uom_id','uom_name','product_barcode_packing','product_barcode_id','product_barcode_barcode',
                'product_barcode_cost_rate as cost_rate','sale_rate','mrp','group_item_id','group_item_name','supplier_id','supplier_name');
        }

        if(isset($request->form_type) && $request->form_type == 'grn') {
            $product = $product->select('product_id','product_name','uom_id','uom_name','product_barcode_packing','product_barcode_id','product_barcode_barcode',
                'product_barcode_cost_rate as cost_rate','sale_rate','mrp','group_item_id','group_item_name','supplier_id','supplier_name');
        }

        if(isset($request->form_type) && $request->form_type == 'product_discount_setup') {
            $product = $product->select('product_id','product_name','uom_id','uom_name','product_barcode_packing','product_barcode_id','product_barcode_barcode',
                'net_tp','sale_rate');
        }
        if(isset($request->form_type) && $request->form_type == 'sale_report') {
            $product = $product->select('product_id','product_name','uom_id','uom_name','product_barcode_packing','product_barcode_id','product_barcode_barcode',
                'net_tp','sale_rate');
        }

        if(isset($request->global_search)){
            if(!empty($request->global_search)){
                $data['global_search'] = $request->global_search;
                $global_search = trim(strtolower(strtoupper($data['global_search'])));
                //$replaced_str = str_replace(' ', '%', $global_search);*/
               
                $p_str = strtoupper($data['global_search']);
                $p_str = str_replace('%2F','/',$p_str);
                $p_str = str_replace('%22','"',$p_str);
                $p_str = str_replace('%2C',',',$p_str);
                $p_str = str_replace("'","''",$p_str);
                $replaced_str = str_replace(' ', '%', trim($p_str));
                $replaced_str = str_replace('%20', '%', trim($replaced_str));

                $product = $product->where(function($qry) use ($replaced_str) {
                    $qry->where(DB::raw('upper(product_name)'),'LIKE',"%".$replaced_str."%")
                        ->orWhere(DB::raw('upper(product_barcode_barcode)'),'LIKE',"%".$replaced_str."%");
                });

            }
            $filterAjax = true;
        }
        if(isset($request->supplier_id)){
            if(!empty($request->supplier_id)){
                $data['supplier_id'] = $request->supplier_id;
                $product = $product->where('supplier_id',$request->supplier_id);
            }
            $filterAjax = true;
        }
        if(isset($request->product_group_id)){
            if(!empty($request->product_group_id)){
                $product = $product->where('GROUP_ITEM_ID',$request->product_group_id);
            }
            $filterAjax = true;
        }
        if(isset($global_search)) {
            $product = $product->orderByRaw("Case
                    WHEN upper(product_name) Like '" .$replaced_str. "' THEN 1
                    WHEN upper(product_name) Like '" .$replaced_str. "%' THEN 2
                    WHEN upper(product_name) Like '%" .$replaced_str. "' THEN 4
                    Else 3
                END")->orderby('product_name');
        }

        
        if(isset($request->form_type) && ($request->form_type == 'product_discount_setup' || $request->form_type == 'change_rate')) {
            $filterAjax = false;
            $where = "";
            if(isset($request->global_search)){
                if(!empty($request->global_search)){
                    $data['global_search'] = $request->global_search;
                    $global_search = trim(strtolower(strtoupper($data['global_search'])));
                    //$replaced_str = str_replace(' ', '%', $global_search);

                    $p_str = strtoupper($data['global_search']);
                    $p_str = str_replace('%2F','/',$p_str);
                    $p_str = str_replace('%22','"',$p_str);
                    $p_str = str_replace('%2C',',',$p_str);
                    $p_str = str_replace("'","''",$p_str);
                    $replaced_str = str_replace(' ', '%', trim($p_str));
                    $replaced_str = str_replace('%20', '%', trim($replaced_str));

                    $where .= " and (upper(product_name) like '%".$replaced_str."%' ";
                    $where .= " OR upper(product_barcode_barcode) like '%".$replaced_str."%' ) ";
                    $filterAjax = true;
                }
            }

            if(isset($request->supplier_id)){
                if(!empty($request->supplier_id)){
                    $where .= " and (supplier_id = ".$request->supplier_id.") ";
                    $filterAjax = true;
                }
            }
            if(isset($request->product_group_id)){
                if(!empty($request->product_group_id)){
                    $where .= " and (product_group_id = ".$request->product_group_id.") ";
                    $filterAjax = true;
                }
            }
            $orderby = "";
            if(isset($global_search)) {
                $orderby = " order by
                        Case
                            WHEN upper(PR.product_name) Like '".$replaced_str."' THEN 1
                            WHEN upper(PR.product_name) Like '".$replaced_str."%' THEN 2
                            WHEN upper(PR.product_name) Like '%".$replaced_str."' THEN 4
                            Else 3
                        END,PR.product_name ";
            }
            if($filterAjax){
                $limit = "";
            }else{
                $limit = " fetch first 100 rows only ";
            }

            $qry = "select 
                PR.product_id,
                PR.product_name,
                PR.uom_id,
                PR.uom_name,
                PR.product_barcode_packing,
                PR.product_barcode_id,
                PR.product_barcode_barcode, 
                nvl(GAGA.sale_rate,0) sale_rate ,
                nvl(GAGA.net_tp,0) net_tp 
            FROM (
                select  
                    product_id , 
                    PRODUCT_BARCODE_ID ,
                    sale_rate,
                    net_tp 
                from 
                    VW_PURC_PRODUCT_BARCODE_RATE 
                WHERE branch_id = ".auth()->user()->branch_id." 
                ) GAGA
                RIGHT OUTER JOIN VW_PURC_PRODUCT_BARCODE PR ON GAGA.PRODUCT_ID =   PR.PRODUCT_ID
                AND GAGA.PRODUCT_BARCODE_ID = PR.PRODUCT_BARCODE_ID 
            where base_barcode = 1 ". $where . $orderby . $limit;
           // dump($qry);
            $product = DB::select($qry);

        }else{
            $product = $product->skip(0)->take(100)->get();
        }

        if(isset($request->form_type) && $request->form_type == 'sale_report') {
            $filterAjax = false;
            $where = "";
            if(isset($request->global_search)){
                if(!empty($request->global_search)){
                    $data['global_search'] = $request->global_search;
                    $global_search = trim(strtolower(strtoupper($data['global_search'])));
                    //$replaced_str = str_replace(' ', '%', $global_search);

                    $p_str = strtoupper($data['global_search']);
                    $p_str = str_replace('%2F','/',$p_str);
                    $p_str = str_replace('%22','"',$p_str);
                    $p_str = str_replace('%2C',',',$p_str);
                    $p_str = str_replace("'","''",$p_str);
                    $replaced_str = str_replace(' ', '%', trim($p_str));
                    $replaced_str = str_replace('%20', '%', trim($replaced_str));

                    $where .= " and (upper(product_name) like '%".$replaced_str."%' ";
                    $where .= " OR upper(product_barcode_barcode) like '%".$replaced_str."%' ) ";
                    $filterAjax = true;
                }
            }
            
            $orderby = "";
            if(isset($global_search)) {
                $orderby = " order by
                        Case
                            WHEN upper(PR.product_name) Like '".$replaced_str."' THEN 1
                            WHEN upper(PR.product_name) Like '".$replaced_str."%' THEN 2
                            WHEN upper(PR.product_name) Like '%".$replaced_str."' THEN 4
                            Else 3
                        END,PR.product_name ";
            }
            if($filterAjax){
                $limit = "";
            }else{
                $limit = " fetch first 100 rows only ";
            }

            $qry = "select 
                PR.product_id,
                PR.product_name,
                PR.uom_id,
                PR.uom_name,
                PR.product_barcode_packing,
                PR.product_barcode_id,
                PR.product_barcode_barcode, 
                nvl(GAGA.sale_rate,0) sale_rate ,
                nvl(GAGA.net_tp,0) net_tp 
            FROM (  
                select  
                    product_id , 
                    PRODUCT_BARCODE_ID ,
                    sale_rate,
                    net_tp 
                from 
                    VW_PURC_PRODUCT_BARCODE_RATE 
                WHERE branch_id = ".auth()->user()->branch_id." 
                ) GAGA
                RIGHT OUTER JOIN VW_PURC_PRODUCT_BARCODE PR ON GAGA.PRODUCT_ID =   PR.PRODUCT_ID
                AND GAGA.PRODUCT_BARCODE_ID = PR.PRODUCT_BARCODE_ID 
            where base_barcode = 1 ". $where . $orderby . $limit;
            
             //   dd($qry);

            $product = DB::select($qry);

        }


        $data['product'] = $product;
        if(isset($request->ajax_req) && $request->ajax_req){
            $filterAjax = true;
        }

        if($filterAjax && !isset($request->open_modal)){

            return $this->jsonSuccessResponse($data, '', 200);

        }else{
            if(isset($request->form_type) && $request->form_type == 'item_tax') {
                return view('purchase.product_smart.product_item_tax.product_modal_body',compact('data'));
            }
            if(isset($request->form_type) && $request->form_type == 'product_discount_setup') {
                return view('purchase.product_smart.discount_setup.product_modal_body',compact('data'));
            }
            if(isset($request->form_type) && $request->form_type == 'change_rate') {
                return view('purchase.product_smart.change_rate.product_modal_body',compact('data'));
            }
            if(isset($request->form_type) && $request->form_type == 'sale_report') {
                return view('reports.product_modal_body',compact('data'));
            }
            if(isset($request->form_type) && $request->form_type == 'shelf_stock') {
                return view('purchase.product_smart.shelf_stock.product_modal_body',compact('data'));
            }
            if(isset($request->form_type) && $request->form_type == 'purchase_order') {
                return view('purchase.purchase_order.product_modal_body',compact('data'));
            }
            if(isset($request->form_type) && $request->form_type == 'grn') {
                return view('purchase.grn.product_modal_body',compact('data'));
            }
            if(isset($request->form_type) && $request->form_type == 'dynamic') {
                return view('purchase.product_smart.product_modal_help.product_modal_body',compact('data'));
            }
        }
    }

    public function openModalProductGroup(){
        $data = [];

        return view('purchase.product_smart.product_modal_help.product_group_tree_modal',compact('data'));
    }

    /**
     * Product Item Tax
     *
     */

    public function viewProductItemTax(){
        $data = [];
        $data['page_data']['title'] = 'Product Item Tax';
        $data['page_data']['path_index'] = '';
        $data['page_data'] = array_merge($data['page_data'], Utilities::newForm());
        $data['tax_group'] = TblDefiTaxGroup::where('tax_group_entry_status',1)->where(Utilities::currentBC())->get();
        $data['gst_clac'] = TblDefiGSTCalculation::where('gst_calculation_entry_status',1)->where(Utilities::currentBC())->get();
        $data['branch'] = TblSoftBranch::where(Utilities::currentBC())->get();
        $data['group_item'] = TblPurcGroupItem::with('last_level')->where('group_item_level',2)->where(Utilities::currentBC())->orderByRaw(DB::raw('lower(group_item_name)'))->get();

        return view('purchase.product_smart.product_item_tax.form', compact('data'));
    }
    public function storeProductItemTax(Request $request)
    {
        $data = [];
        if(!isset($request->product_id)){
            return $this->jsonErrorResponse($data, 'at least One Product is required');
        }
       /*$validator = Validator::make($request->all(), [
            'branch_id' => 'required|not_in:0',
        ],[
            'branch_id.required' => 'Branch is required',
            'branch_id.not_in' => 'Branch is required',
        ]);

        if ($validator->fails()) {
            $data['validator_errors'] = $validator->errors();
            $msg = "Some fields are required";
            foreach ($data['validator_errors']->messages() as $validator_errors){
                $msg = $validator_errors[0];
                return $this->jsonErrorResponse($data, $msg);
                break;
            }
        }
        */
        $tax_group_id = "";
        if(isset($request->tax_group_id) && !empty($request->tax_group_id)){
            $tax_group_id = $request->tax_group_id;
        }
        $gst_calculation_id = "";
        if(isset($request->gst_calculation_id) && !empty($request->gst_calculation_id)){
            $gst_calculation_id = $request->gst_calculation_id;
        }
        $hs_code = "";
        if(isset($request->hs_code) && !in_array($request->hs_code,[""]) ){
            $hs_code = $request->hs_code;
        }

        DB::beginTransaction();
        try{
            /*$all_branches = false;
            if(in_array('all',$request->branch_id)){
                $all_branches = true;
            }
            $branch_ids = [];
            if($all_branches){
                $branch_ids = TblSoftBranch::pluck('branch_id')->toArray();
            }else{
                $branch_ids[] = $request->branch_id;
            }
            */

            $branch_ids = TblSoftBranch::pluck('branch_id')->toArray();
            $rateUpdate = true;

            if($rateUpdate){
                if(!empty($branch_ids) && count($branch_ids) != 0 && count($request->product_id) != 0) {
                    foreach ($branch_ids as $branch_id){
                        foreach ($request->product_id as $product_id){
                            $barcodes = TblPurcProductBarcode::where('product_id',$product_id)->get();
                            
                            foreach ($barcodes as $barcode){
                               // $rate = TblPurcProductBarcodePurchRate::where('product_id',$product_id)
                               $tax_value = ViewPurcProductBarcodeRate::where('product_id',$product_id)
                               ->where('product_barcode_id',$barcode->product_barcode_id)
                               //->where('branch_id',$branch_id)
                               ->first();
                                
                               if(!empty($tax_value)){
                                    $tax_group_value = $tax_value->tax_group_value;
                                }else{
                                    $tax_group_value = "";
                                }

                               $rate = TblPurcProductBarcodePurchRate::where('product_id',$product_id)
                                    ->where('product_barcode_id',$barcode->product_barcode_id)
                                    ->where('branch_id',$branch_id)
                                    ->first();
                                
                                $getTaxGroup = TblDefiTaxGroup::where('tax_group_id',$tax_group_id)->first();
                               
                                $inclusive_tax_price = "";
                                $sale_rate = 0;
                                if(!empty($rate->sale_rate)){
                                    $sale_rate = $rate->sale_rate;
                                }
                                $tax_rate = 0;
                                if(!empty($tax_group_id) && isset($getTaxGroup->tax_group_value)){
                                    $tax_rate = $getTaxGroup->tax_group_value;
                                    $tax_amount = $sale_rate / 100 * $tax_rate;
                                    $inclusive_tax_price = $sale_rate + $tax_amount;
                                }
                                
                                if(!empty($rate)){
                                    if(!empty($tax_group_id)){
                                        $rate->tax_group_id = $tax_group_id;
                                    }
                                    if(!empty($inclusive_tax_price)){
                                        $rate->inclusive_tax_price = $inclusive_tax_price;
                                    }
                                    if(!empty($gst_calculation_id)){
                                        $rate->gst_calculation_id = $gst_calculation_id;
                                    }
                                    //if(!empty($hs_code)){
                                        $rate->hs_code = $hs_code;
                                    //}
                                    //if(!empty($tax_rate)){
                                        $rate->tax_rate = $tax_rate;
                                        $rate->sale_tax_rate = $tax_rate;
                                    //}

                                    $rate->save();
                                }else{
                                    TblPurcProductBarcodePurchRate::create([
                                        'product_barcode_purch_id' => Utilities::uuid(),
                                        'product_id' => $product_id,
                                        'product_barcode_id' => $barcode->product_barcode_id,
                                        'product_barcode_barcode' => $barcode->product_barcode_barcode,
                                        'tax_group_id' => $tax_group_id,
                                        'tax_rate' => $tax_rate,
                                        'sale_tax_rate' => isset($tax_rate)?$tax_rate:"",
                                        'inclusive_tax_price' => $inclusive_tax_price,
                                        'gst_calculation_id' => $gst_calculation_id,
                                        'hs_code' => isset($hs_code)?$hs_code:"",
                                        'business_id' => auth()->user()->business_id,
                                        'company_id' => auth()->user()->company_id,
                                        'branch_id' => $branch_id,
                                    ]);
                                }
                            }

                            TblPurcProduct::where('product_id',$product_id)->update([
                                'update_id' => Utilities::uuid()
                            ]);
                        }
                    }
                }
            }
        }catch (Exception $e){
            DB::rollback();
            return $this->jsonErrorResponse($data, $e->getMessage(), 200);
        }
        DB::commit();


        return $this->jsonSuccessResponse($data, 'Item Tax successfully updated', 200);
        return view('purchase.product_smart.product_item_tax.form', compact('data'));
    }
    public function productListProductItemTax(Request $request){
        $data = [];
        if($request->hs_code != 1)
        {
            if(empty($request->supplier_id) && empty($request->group_item_id)){
                return $this->jsonErrorResponse($data, 'at least One is required between Group Item Or Supplier');
            }
        }
        if(empty($request->branch_id) && count($request->branch_id) == 0){
            return $this->jsonErrorResponse($data, 'Branch is required');
        }
        DB::beginTransaction();
        try{
            $where = "";
            /*
            if(!empty($request->start_date) && !empty($request->end_date)){
                $where .= " and (created_at between to_date ('".$request->start_date."', 'dd/mm/yyyy') and to_date ('".$request->end_date."', 'dd/mm/yyyy'))";
            }*/
            if($request->hs_code != 1)
            {
                if(!empty($request->supplier_id)){
                    $where .= " and supplier_id = ".$request->supplier_id;
                }
                if(!empty($request->group_item_id)){
                    $where .= " and group_item_id = ".$request->group_item_id;
                }
            }
            if($request->hs_code == 1){
                $where .= " and (hs_code is null or hs_code <> 0)";
            }
            $all_branches = false;
            if(in_array('all',$request->branch_id)){
                $all_branches = true;
            }
            $branch_ids = [];
            $branch_id = "";
            if($all_branches){
                $branch_ids = TblSoftBranch::pluck('branch_id')->toArray();
                $branch_id .= " branch_id in (".implode(",",$branch_ids).") ";
            }else{
                $branch_id .= " branch_id in (".implode(",",$request->branch_id).") ";
            }
            /*$qry = "select PR.product_id,PR.product_name,PR.uom_name,PR.product_barcode_packing,PR.product_barcode_barcode,
                GAGA.cost_rate,GAGA.sale_rate,GAGA.mrp,group_item_id,group_item_name,supplier_id,supplier_name, base_barcode,
            brand_name,hs_code,tax_group_name,gst_calculation_name
             FROM (  select  product_id , BRANCH_ID , PRODUCT_BARCODE_ID ,
                product_barcode_cost_rate as cost_rate,sale_rate,mrp,hs_code,tax_group_name,gst_calculation_name
                from VW_PURC_PRODUCT_BARCODE_RATE 
            ) GAGA
            RIGHT OUTER JOIN VW_PURC_PRODUCT_BARCODE PR ON GAGA.PRODUCT_ID =   PR.PRODUCT_ID AND
            GAGA.PRODUCT_BARCODE_ID =   PR.PRODUCT_BARCODE_ID 
            where (created_at between to_date ('".$request->start_date."', 'dd/mm/yyyy') and to_date ('".$request->end_date."', 'dd/mm/yyyy'))
                $where 
            order by product_name";
            */

        $qry = "SELECT 
            PR.product_id,
            PR.product_name,
            PR.uom_name,
            PR.product_barcode_packing,
            PR.product_barcode_barcode,
            GAGA.cost_rate,
            GAGA.sale_rate,
            GAGA.mrp,
            group_item_id,
            group_item_name,
            supplier_id,
            supplier_name,
            base_barcode,
            brand_name,
            hs_code,
            tax_group_value,
            tax_group_name,
            gst_calculation_name 
        FROM(
            SELECT 
                product_id,
                PRODUCT_BARCODE_ID,
                max(net_tp) AS cost_rate,
                max(sale_rate) sale_rate,
                max(mrp) mrp,
                max(hs_code) hs_code,
                max(tax_group_value) tax_group_value,
                max(tax_group_name) tax_group_name,
                max(gst_calculation_name)  gst_calculation_name
            FROM
                VW_PURC_PRODUCT_BARCODE_RATE
            group by product_id,
                PRODUCT_BARCODE_ID
            ) GAGA 
            RIGHT OUTER JOIN VW_PURC_PRODUCT_BARCODE PR 
              ON GAGA.PRODUCT_ID = PR.PRODUCT_ID 
              AND GAGA.PRODUCT_BARCODE_ID = PR.PRODUCT_BARCODE_ID 
          WHERE (created_at between to_date ('".$request->start_date."', 'dd/mm/yyyy') and to_date ('".$request->end_date."', 'dd/mm/yyyy'))
                $where  
          ORDER BY product_name ";


//dd($qry);
            $data['products'] = DB::select($qry);

        }catch (Exception $e){
            DB::rollback();
            return $this->jsonErrorResponse($data, $e->getMessage(), 200);
        }
        DB::commit();

        return $this->jsonSuccessResponse($data, 'Data loaded successfully', 200);

    }
    /**
     * Product Shelf Stock
     *
     */
    public function viewProductShelfStock(){
        $data = [];
        $data['page_data']['title'] = 'Product Shelf Stock';
        $data['page_data'] = array_merge($data['page_data'], Utilities::newForm());
        $data['branch'] = TblSoftBranch::where(Utilities::currentBC())->get();
        $data['group_item'] = ViewPurcGroupItem::where('group_item_level',3)->orderBy('group_item_name_string')->where(Utilities::currentBC())->get();

        return view('purchase.product_smart.shelf_stock.form', compact('data'));
    }

    public function storeProductShelfStock(Request $request){
        $data = [];
        $validator = Validator::make($request->all(), [
            'branch_id' => 'required|not_in:0',
        ],[
            'branch_id.required' => 'Branch is required',
            'branch_id.not_in' => 'Branch is required',
        ]);

        if ($validator->fails()) {
            $data['validator_errors'] = $validator->errors();
            $msg = "Some fields are required";
            foreach ($data['validator_errors']->messages() as $validator_errors){
                $msg = $validator_errors[0];
                return $this->jsonErrorResponse($data, $msg);
                break;
            }
        }
        if(count($request->pd) == 0){
            return $this->jsonErrorResponse($data, 'Product is required');
        }
        if(empty($request->shelf_stock_max_qty)
        && empty($request->shelf_stock_min_qty)
        && empty($request->shelf_stock_dept_qty)
        && empty($request->shelf_stock_face_qty)
        && empty($request->shelf_stock_record_point)
        ){
            return $this->jsonErrorResponse($data, 'Required to fill any input field');
        }
        DB::beginTransaction();
        try{

            foreach ($request->pd as $pd){
                if(isset($pd['add_prod'])){
                    $exists = TblPurcProductBarcodeDtl::where(['branch_id'=>$request->branch_id,'product_barcode_id'=>$pd['product_barcode_id']])->exists();

                    if($exists){
                        $bdtl = TblPurcProductBarcodeDtl::where(['branch_id'=>$request->branch_id,'product_barcode_id'=>$pd['product_barcode_id']])->first();
                        if(!empty($request->shelf_stock_min_qty)){
                            $bdtl->product_barcode_stock_limit_max_qty = $request->shelf_stock_max_qty;
                        }
                        if(!empty($request->shelf_stock_min_qty)){
                            $bdtl->product_barcode_stock_limit_min_qty = $request->shelf_stock_min_qty;
                        }
                        if(!empty($request->shelf_stock_face_qty)){
                            $bdtl->product_barcode_shelf_stock_dept_qty = $request->shelf_stock_face_qty;
                        }
                        if(!empty($request->shelf_stock_face_qty)){
                            $bdtl->product_barcode_shelf_stock_face_qty = $request->shelf_stock_face_qty;
                        }
                        if(!empty($request->shelf_stock_record_point)){
                            $bdtl->product_barcode_shelf_stock_reorder_point = $request->shelf_stock_record_point;
                        }
                        $bdtl->save();

                    }
                }
            }

        }catch (Exception $e){
            DB::rollback();
            return $this->jsonErrorResponse($data, $e->getMessage(), 200);
        }
        DB::commit();

        return $this->jsonSuccessResponse($data, 'Barcode Stock Shelf successfully updated', 200);
    }

    /**
     * Product Discount Setup
     *
     */
    public function viewProductDiscountSetup(Request $request, $id=null){
        $data = [];
        $data['page_data']['title'] = 'Product Discount Setup';
        $data['page_data'] = array_merge($data['page_data'], Utilities::newForm());
        $data['branch'] = TblSoftBranch::where(Utilities::currentBC())->get();
        $data['group_item'] = ViewPurcGroupItem::where('group_item_level',3)->orderByRaw(DB::raw('lower(group_item_name)'))->where(Utilities::currentBC())->get();
        $constants = TblDefiConstants::whereIn('constants_type',['sale_type','discount_type','promotion_type'])->get();
        $data['sale_type'] = [];
        $data['discount_type'] = [];
        $data['promotion_type'] = [];
        foreach ($constants as $constant){
            if($constant->constants_type == 'sale_type'){
                $data['sale_type'][] = $constant;
            }
            if($constant->constants_type == 'discount_type'){
                $data['discount_type'][] = $constant;
            }
            if($constant->constants_type == 'promotion_type'){
                $data['promotion_type'][] = $constant;
            }
        }
        if(isset($id)){
            if(TblSaleDiscountSetup::where('discount_setup_id',$id)->where(Utilities::currentBCB())->exists()){
                $data['page_data'] = array_merge($data['page_data'], Utilities::editForm());
                $data['permission'] = self::$menu_dtl_id.'-edit';
                $data['id'] = $id;
                $data['current'] = TblSaleDiscountSetup::where('discount_setup_id',$id)->where(Utilities::currentBCB())->first();
                // dd($data['current']->toArray());
                if(empty($data['current'])){
                    abort('404');
                }
                $data['discount_setup_code'] = $data['current']->discount_setup_code;
            }else{
                abort('404');
            }
        }else{
            $data['permission'] = self::$menu_dtl_id.'-create';
            $data['page_data'] = array_merge($data['page_data'], Utilities::newForm());
            $doc_data = [
                'biz_type'          => 'business',
                'model'             => 'Sale\TblSaleDiscountSetup',
                'code_field'        => 'discount_setup_code',
                'code_prefix'       => strtoupper('DISC')
            ];
            $data['discount_setup_code'] = Utilities::documentCode($doc_data);
        }
        $doc_data = [
            'biz_type'          => 'business',
            'model'             => 'Sale\TblSaleDiscountSetup',
            'code_field'        => 'discount_setup_code',
            'code_prefix'       => strtoupper('DISC')
        ];
        $data['document_code'] = Utilities::documentCode($doc_data);
        $data['membership_type'] = TblDefiMembershipType::where('membership_type_entry_status',1)->get();
        //dd($data['sale_type']);
        return view('purchase.product_smart.discount_setup.form', compact('data'));
    }

    public function storeProductDiscountSetup(Request $request, $id=null)
    {
        $data = [];
        $validator = Validator::make($request->all(), [
            'discount_setup_title' => 'required|max:100|unique:tbl_sale_discount_setup',
            'start_date' => 'required|date|date_format:d-m-Y H:i|before:end_date',
            'end_date' => 'required|date|date_format:d-m-Y H:i|after:start_date',
            'branch_id' => 'required|not_in:0',
            'sale_type' => 'required|not_in:0',
            'discount_type' => 'required|not_in:0',
            'promotion_type' => 'required|not_in:0',
        ],[
            'discount_setup_title.required' => 'Title is required',
            'discount_setup_title.max' => 'Title max length is 100',
            'kt_dtpicker_start_date.required' => 'Start Date is required',
            'kt_dtpicker_start_date.date' => 'Start Date must be date time format',
            'kt_dtpicker_start_date.date_format' => 'Start Date must be date time format',
            'kt_dtpicker_start_date.before' => 'Start Date must be less then End Date',

            'kt_dtpicker_end_date.required' => 'End Date is required',
            'kt_dtpicker_end_date.date' => 'End Date must be date time format',
            'kt_dtpicker_end_date.date_format' => 'End Date must be date time format',
            'kt_dtpicker_end_date.after' => 'End Date must be greater then Start Date',

            'branch_id.required' => 'Branch is required',
            'branch_id.not_in' => 'Branch is required',

            'sale_type.required' => 'Sale type is required',
            'sale_type.not_in' => 'Sale type is required',

            'discount_type.required' => 'Discount type is required',
            'discount_type.not_in' => 'Discount type is required',

            'promotion_type.required' => 'Promotion type is required',
            'promotion_type.not_in' => 'Promotion type is required',

        ]);

        if ($validator->fails()) {
            $data['validator_errors'] = $validator->errors();
            $msg = "Some fields are required";
            foreach ($data['validator_errors']->messages() as $validator_errors){
                $msg = $validator_errors[0];
                return $this->jsonErrorResponse($data, $msg);
                break;
            }
        }

        $discount_qty = '';
        $discount_perc = '';
        $flat_discount_qty = '';
        $flat_discount_amount = '';
        $slab_base = 0;
        if($request->promotion_type == 'cash_discount'){
            $discount_qty = $request->discount_qty;
            $discount_perc = $request->discount_perc;
            $flat_discount_qty = $request->flat_discount_qty;
            $flat_discount_amount = $request->flat_discount_amount;
            $slab_base = isset($request->slab_base)?1:0;
            if(empty($discount_qty) && empty($discount_perc) && empty($flat_discount_qty) && empty($flat_discount_amount)){
                return $this->jsonErrorResponse($data, 'Discount Qty is required');
            }
            $discount_block = false;
            if(!empty($discount_qty) || !empty($discount_perc)){
                $discount_block = true;
            }
            if($discount_block) {
                if(empty($discount_qty)){
                    return $this->jsonErrorResponse($data, 'Discount Qty is required');
                }
                if(empty($discount_perc)){
                    return $this->jsonErrorResponse($data, 'Discount Percent is required');
                }
            }
            $flat_block = false;
            if(!empty($flat_discount_qty) || !empty($flat_discount_amount)){
                $flat_block = true;
            }
            if($flat_block) {
                if(empty($flat_discount_qty)){
                    return $this->jsonErrorResponse($data, 'Flat Discount Qty is required');
                }
                if(empty($flat_discount_amount)){
                    return $this->jsonErrorResponse($data, 'Flat Discount Amount is required');
                }
            }
        }


        $amount_for_point = '';
        $point_quantity = '';
        if($request->promotion_type == 'reward_point'){

            $amount_for_point = $request->amount_for_point;
            $point_quantity = $request->point_quantity;

        }
        $is_all_member = isset($request->is_all_member)?1:0;
        $is_with_member = isset($request->is_with_member)?1:0;
        $is_without_member = isset($request->is_without_member)?1:0;
        if(empty($is_all_member) && empty($is_with_member) && empty($is_without_member)){
            return $this->jsonErrorResponse($data, 'Category Scheme Type must be checked');
        }
        if(!empty($is_with_member) &&
            (!isset($request->membership_type) || count($request->membership_type) == 0)){
            return $this->jsonErrorResponse($data, 'Any membership type is required');
        }
        if($is_all_member){
            $is_with_member = 1;
            $is_without_member = 1;
            $membership_types = TblDefiMembershipType::where('membership_type_entry_status',1)->pluck('membership_type_id')->toArray();
        }

        DB::beginTransaction();
        try{
            $doc_data = [
                'biz_type'          => 'business',
                'model'             => 'Sale\TblSaleDiscountSetup',
                'code_field'        => 'discount_setup_code',
                'code_prefix'       => strtoupper('DISC')
            ];
            $document_code = Utilities::documentCode($doc_data);

            $all_branches = false;
            if(in_array('all',$request->branch_id)){
                $all_branches = true;
            }
            if($all_branches){
                $branch_ids = TblSoftBranch::pluck('branch_id')->toArray();
            }else{
                $branch_ids = $request->branch_id;
            }
            $discount_setup_entry_saved = false;
            if(!empty($branch_ids) && count($branch_ids) != 0) {
                $group_item = false;
                if(isset($request->group_item_id) && !empty($request->group_item_id)){
                    $discount_setup_entry_saved = true;
                    $group_item = true;
                    $discount_setup_id = Utilities::uuid();
                    foreach ($branch_ids as $branch_id){
                        $branches = TblSchemeBranches::updateOrCreate(
                        ['discount_setup_id' => $discount_setup_id,
                        'discount_setup_title'	=>	$request->discount_setup_title
                        ],
                        [
                            'scheme_id'	=>	Utilities::uuid(),
                            'branch_id'	=>	$branch_id,
                            'is_active'	=>	1,
                            'start_date'	=>	date('Y-m-d H:i',strtotime($request->start_date)),
                            'end_date'	=>	date('Y-m-d H:i',strtotime($request->end_date)),
                            'min_sale_amount'	=>	$amount_for_point,
                            'loyalty_rate'	=>	$point_quantity,
                            'slab_base'	=>	$slab_base,
                            'is_with_member'	=>	$is_with_member,
                            'is_without_member'	=>	$is_without_member ,
                        ]);
                    }
                    foreach ($request->group_item_id as $group_item_id){
                        $discount = TblSaleDiscountSetup::updateOrCreate(
                        ['discount_setup_id' => $discount_setup_id,
                        'discount_setup_title'	=>	$request->discount_setup_title
                        ],
                        [
                            'discount_setup_id'	=>	$discount_setup_id,
                            'discount_setup_row_id'	=>	Utilities::uuid(),
                            'discount_setup_code'	=>	$document_code,
                            'discount_setup_type'	=>	'group_item',
                            'start_date'	=>	date('Y-m-d H:i',strtotime($request->start_date)),
                            'end_date'	=>	date('Y-m-d H:i',strtotime($request->end_date)),
                            'sale_type'	=>	$request->sale_type,
                            'discount_type'	=>	$request->discount_type,
                            'promotion_type'	=>	$request->promotion_type,
                            'discount_qty'	=>	$discount_qty,
                            'discount_perc'	=>	$discount_perc,
                            'flat_discount_qty'	=>	$flat_discount_qty,
                            'flat_discount_amount'	=>	$flat_discount_amount,
                            'sr_no'	=>	1,
                            'product_id'	=>	'',
                            'product_barcode_id'	=>	'',
                            'product_barcode_barcode'	=>	'',
                            'uom_id'	=>	'',
                            'packing'	=>	'',
                            'group_item_id'	=>	$group_item_id,
                            'cost_rate'	=>	'',
                            'mrp'	=>	'',
                            'sale_rate'	=>	'',
                            'gp_amount'	=>	'',
                            'gp_perc'	=>	'',
                            'disc_amount'	=>	'',
                            'disc_perc'	=>	'',
                            'after_disc_gp_amount'	=>	'',
                            'after_disc_gp_perc'	=>	'',
                            'is_active'	=>	1,
                            'remarks'	=>	$request->discount_setup_remarks,
                            'user_id'	=>	auth()->user()->id,
                            'business_id'	=>	auth()->user()->business_id,
                            'company_id'	=>	auth()->user()->company_id,
                            'branch_id'	=>	auth()->user()->branch_id,
                            'amount_for_point'	=>	$amount_for_point,
                            'point_quantity'	=>	$point_quantity,
                            'slab_base'	=>	$slab_base,
                            'is_with_member'	=>	$is_with_member,
                            'is_without_member'	=>	$is_without_member ,
                        ]);
                    }
                    if(!empty($is_all_member)){
                        foreach ($membership_types as $membership_type) {
                            TblSaleDiscountSetupMembership::create([
                                'discount_setup_membership_id' => Utilities::uuid(),
                                'discount_setup_id' => $discount_setup_id,
                                'membership_type_id' => $membership_type,
                            ]);
                        }
                    }else{
                        if (!empty($is_with_member) && isset($request->membership_type) && count($request->membership_type) != 0) {
                            foreach ($request->membership_type as $membership_type) {
                                TblSaleDiscountSetupMembership::create([
                                    'discount_setup_membership_id' => Utilities::uuid(),
                                    'discount_setup_id' => $discount_setup_id,
                                    'membership_type_id' => $membership_type,
                                ]);
                            }
                        }
                    }
                }
                if (isset($request->pd) && count($request->pd) != 0 && !$group_item) {
                    $discount_setup_entry_saved = true;
                    $discount_setup_id = Utilities::uuid();
                    foreach ($branch_ids as $branch_id) {
                        $branches1 = TblSchemeBranches::updateOrCreate(
                        ['discount_setup_id' => $discount_setup_id,
                        'discount_setup_title' => $request->discount_setup_title
                        ],
                        [
                            'scheme_id' => Utilities::uuid(),
                            'branch_id' => $branch_id,
                            'is_active' => 1,
                            'start_date'	=>	date('Y-m-d H:i',strtotime($request->start_date)),
                            'end_date'	=>	date('Y-m-d H:i',strtotime($request->end_date)),
                            'min_sale_amount'	=>	$amount_for_point,
                            'loyalty_rate'	=>	$point_quantity,
                            'slab_base'	=>	$slab_base,
                            'is_with_member'	=>	$is_with_member,
                            'is_without_member'	=>	$is_without_member ,
                        ]);
                       // dump($branches1);
                    }

                    $sr_no = 1;
                    foreach ($request->pd as $pd) {
                        $discount1 = TblSaleDiscountSetup::updateOrCreate(
                            ['discount_setup_id' => $discount_setup_id,
                            'discount_setup_title' => $request->discount_setup_title
                            ],
                            [
                            'discount_setup_row_id' => Utilities::uuid(),
                            'discount_setup_code' => $document_code,
                            'discount_setup_type' => 'product',
                            'start_date' => date('Y-m-d H:i', strtotime($request->start_date)),
                            'end_date' => date('Y-m-d H:i', strtotime($request->end_date)),
                            'sale_type' => $request->sale_type,
                            'discount_type' => $request->discount_type,
                            'promotion_type' => $request->promotion_type,
                            'discount_qty' => $discount_qty,
                            'discount_perc' => $discount_perc,
                            'flat_discount_qty' => $flat_discount_qty,
                            'flat_discount_amount' => $flat_discount_amount,
                            'sr_no' => $sr_no,
                            'product_id' => $pd['product_id'],
                            'product_barcode_id' => $pd['product_id'],
                            'product_barcode_barcode' => $pd['product_id'],
                            'uom_id' => $pd['uom_id'],
                            'packing' => $pd['pd_packing'],
                            'group_item_id' => $pd['cate_last_level_id'],
                            'cost_rate' => $pd['current_tp'],
                            'mrp' => $pd['mrp'],
                            'sale_rate' => $pd['sale_rate'],
                            'gp_amount' => $pd['gp_rate'],
                            'gp_perc' => $pd['gp_perc'],
                            'disc_amount' => $pd['disc_amt'],
                            'x' => $pd['disc_price'],
                            'after_disc_gp_amount' => $pd['after_disc_gp_amt'],
                            'after_disc_gp_perc' => $pd['after_disc_gp_perc'],
                            'is_active' => 1,
                            'remarks' => $request->discount_setup_remarks,
                            'user_id' => auth()->user()->id,
                            'business_id' => auth()->user()->business_id,
                            'company_id' => auth()->user()->company_id,
                            'branch_id' => auth()->user()->branch_id,
                            'amount_for_point' => $amount_for_point,
                            'point_quantity' => $point_quantity,
                            'slab_base' => $slab_base,
                            'is_with_member'	=>	$is_with_member,
                            'is_without_member'	=>	$is_without_member ,0,
                        ]);
                        $sr_no = $sr_no + 1;
                       // dd($discount1);
                    }

                    if(!empty($is_all_member)){
                        foreach ($membership_types as $membership_type) {
                            TblSaleDiscountSetupMembership::create([
                                'discount_setup_membership_id' => Utilities::uuid(),
                                'discount_setup_id' => $discount_setup_id,
                                'membership_type_id' => $membership_type,
                            ]);
                        }
                    }else{
                        if (!empty($is_with_member) && isset($request->membership_type) && count($request->membership_type) != 0) {
                            foreach ($request->membership_type as $membership_type) {
                                TblSaleDiscountSetupMembership::create([
                                    'discount_setup_membership_id' => Utilities::uuid(),
                                    'discount_setup_id' => $discount_setup_id,
                                    'membership_type_id' => $membership_type,
                                ]);
                            }
                        }
                    }
                }
            }

            if(empty($discount_setup_entry_saved)){
                return $this->jsonErrorResponse($data,'Please add Item or select Product Group', 200);
            }
        }catch (Exception $e) {
            DB::rollback();
            return $this->jsonErrorResponse($data, $e->getMessage(), 200);
        }
        DB::commit();
        if(isset($id)){
            $data = array_merge($data, Utilities::returnJsonEditForm());
            $data['redirect'] = '/smart-product/product-discount-setup/form';
            return $this->jsonSuccessResponse($data, trans('message.update'), 200);
        }else{
            $data = array_merge($data, Utilities::returnJsonNewForm());
            $data['redirect'] = '/smart-product/product-discount-setup/form';
            return $this->jsonSuccessResponse($data, trans('message.create'), 200);
        }
    }


    /**
     * Supplier wise Product Detail
     *
     */
    public function viewSupplierWiseProductDetail(){
        $data = [];
        $data['page_data']['title'] = 'Vendor wise Product';
        $data['supplier'] = TblPurcSupplier::where('supplier_entry_status',1)->get();
        return view('purchase.product_smart.supplier_wise_products.form', compact('data'));
    }
    public function getSupplierWiseProductDetail(Request $request){
        $data = [];
        $supplier_id = $request->supplier_id;
        if(TblPurcSupplier::where('supplier_id',$supplier_id)->exists()){
            $qry = "select foc.*,p.product_name,pb.product_barcode_barcode from TBL_PURC_PRODUCT_FOC foc
                        join tbl_purc_product p on p.product_id = foc.product_id
                        join tbl_purc_product_barcode pb on pb.product_barcode_id = foc.product_barcode_id where foc.supplier_id = $supplier_id fetch first 100 rows only";

            $supplier = DB::select($qry);
            $data['supplier'] = $supplier;
        }
        return $this->jsonSuccessResponse($data, 'Data Loaded', 200);
    }

    /**
     * Product TP Analysis
     *
     */
     public function viewProductTPAnalysis(){
        $data = [];
        $data['page_data']['title'] = 'TP Analysis';
        
        $user = User::where('id', auth()->user()->id)->where('user_type','erp')->where('user_entry_status',1)->where(Utilities::currentBC())->first();
        if($user->apply_warehouse == 1)
        {
            $data['branch'] = TblSoftBranch::where(Utilities::currentBC())->get();
        }else{
            $data['branch'] = Utilities::getApplyBranches();
        }

        return view('purchase.product_smart.tp_analysis.form', compact('data'));
    }
     public function getProductsTpAnalysis(Request $request){
        $data = [];
        $validator = Validator::make($request->all(), [
            'branch_id' => 'required|not_in:0',
            'product_barcode_barcode' => 'required',
        ]);
        if ($validator->fails()) {
            $data['validator_errors'] = $validator->errors();
            return $this->jsonErrorResponse($data, trans('message.required_fields'), 200);
        }
         DB::beginTransaction();
         try{
            $user = User::where('id', auth()->user()->id)->where('user_type','erp')->where('user_entry_status',1)->where(Utilities::currentBC())->first();
            if($user->apply_warehouse == 1)
            {
                $branch = TblSoftBranch::pluck('branch_id')->toArray();
            }else{
                $branch = TblSoftBranch::where('branch_id','!=', '9')->pluck('branch_id')->toArray();
            }

                $all_branches = false;
                if($request->tp_inv_type == "grn"){
                    $all_branches = true;
                }
                
                $branch_ids = [];
                if($all_branches){
                    $branch_id[] = auth()->user()->branch_id;
                    $branch_ids = $branch;
                    $all_branch_id[] = auth()->user()->branch_id;
                }else{
                    if($request->branch_id == "all"){
                        $branch_id[] = auth()->user()->branch_id;
                        $branch_ids = $branch;
                        $all_branch_id[] = auth()->user()->branch_id;
                    }else{
                        $branch_id[] = $request->branch_id;
                        $branch_ids[] = $request->branch_id;
                        $all_branch_id[] = $request->branch_id;
                    }
                }
                $product_barcode_barcode = trim($request->product_barcode_barcode);

                $data['product'] = DB::selectOne("SELECT product_id,product_name,product_barcode_id,product_barcode_cost_rate,sale_rate,mrp,net_tp
                            FROM VW_PURC_PRODUCT_BARCODE_RATE where product_barcode_barcode like '".$product_barcode_barcode."' and BRANCH_ID IN (".implode(",",$all_branch_id).")");

                if(!empty($data['product'])){
                    $product_id = $data['product']->product_id;
                    $qry = "SELECT VW_PURC_GRN.* FROM VW_PURC_GRN where PRODUCT_ID = $product_id
                        AND BUSINESS_ID = ".auth()->user()->business_id." AND COMPANY_ID = ".auth()->user()->company_id." AND BRANCH_ID IN (".implode(",",$branch_id).") AND lower(GRN_TYPE) = 'grn'
                        AND TBL_PURC_GRN_DTL_NET_TP > 0 ORDER BY TBL_PURC_GRN_DTL_NET_TP DESC OFFSET 0 ROWS FETCH NEXT 1 ROWS ONLY";
                    
                    $data['max_net_tp'] = DB::selectOne($qry);
                   
                    $qry = "SELECT VW_PURC_GRN.* FROM VW_PURC_GRN where PRODUCT_ID = $product_id
                    AND BUSINESS_ID = ".auth()->user()->business_id." AND COMPANY_ID = ".auth()->user()->company_id." AND BRANCH_ID IN (".implode(",",$branch_id).") AND lower(GRN_TYPE) = 'grn'
                    AND TBL_PURC_GRN_DTL_NET_TP > 0 ORDER BY TBL_PURC_GRN_DTL_NET_TP OFFSET 0 ROWS FETCH NEXT 1 ROWS ONLY";
                    //dd($qry);
                    $data['min_net_tp'] = DB::selectOne($qry);

                    $qry = "SELECT VW_PURC_GRN.* FROM VW_PURC_GRN where PRODUCT_ID = $product_id
                    AND BUSINESS_ID = ".auth()->user()->business_id." AND COMPANY_ID = ".auth()->user()->company_id." AND BRANCH_ID IN (".implode(",",$branch_id).") AND lower(GRN_TYPE) = 'grn'
                    AND TBL_PURC_GRN_DTL_NET_TP > 0 ORDER BY created_at desc OFFSET 0 ROWS FETCH NEXT 1 ROWS ONLY";
                    $data['current_net_tp'] = DB::selectOne($qry);

                    $qry = "SELECT VW_PURC_GRN.* FROM VW_PURC_GRN where PRODUCT_ID = $product_id
                    AND BUSINESS_ID = ".auth()->user()->business_id." AND COMPANY_ID = ".auth()->user()->company_id." AND BRANCH_ID IN (".implode(",",$branch_id).") AND lower(GRN_TYPE) = 'grn'
                    AND TBL_PURC_GRN_DTL_NET_TP > 0 ORDER BY created_at desc OFFSET 0 ROWS FETCH NEXT 1 ROWS ONLY";
                    $data['last_net_tp'] = DB::selectOne($qry);

                    $qry = "SELECT VW_PURC_GRN.* FROM VW_PURC_GRN where PRODUCT_ID = $product_id
                    AND BUSINESS_ID = ".auth()->user()->business_id." AND COMPANY_ID = ".auth()->user()->company_id." AND BRANCH_ID IN (".implode(",",$branch_ids).") AND lower(GRN_TYPE) = 'grn'
                    AND TBL_PURC_GRN_DTL_NET_TP > 0 ORDER BY created_at desc OFFSET 0 ROWS FETCH NEXT 10 ROWS ONLY";
                    $data['vendor_last_purc'] = DB::select($qry);

                    $now = new \DateTime("now");
                    $today_format = $now->format("d-m-Y");
                    $date = date('Y-m-d', strtotime($today_format));
                    $branch_id='';
                    if($request->branch_id != "all"){
                        $branch_id = $request->branch_id;
                    }
                    $arr = [
                        $product_id,
                        $data['product']->product_barcode_id,
                        auth()->user()->business_id,
                        auth()->user()->company_id,
                        $branch_id,
                        '',
                        $date
                    ];
                    $store_stock =  collect(DB::select('SELECT get_stock_current_qty_date(?,?,?,?,?,?,?) AS code from dual', $arr))->first()->code;
                    $data['store_stock'] = $store_stock;

                }else{
                    return $this->jsonErrorResponse($data, 'Barcode not exists', 200);
                }

         } catch (Exception $e) {
             DB::rollback();
             return $this->jsonErrorResponse($data, $e->getMessage(), 200);
         }
         DB::commit();

         return $this->jsonSuccessResponse($data, 'Data Load', 200);
     }

}
