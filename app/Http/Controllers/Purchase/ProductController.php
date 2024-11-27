<?php

namespace App\Http\Controllers\Purchase;

use App\Http\Controllers\Controller;
use App\Library\Utilities;
use App\Models\TblDefiColor;
use App\Models\TblDefiSize;
use App\Models\TblDefiTags;
use App\Models\TblPurcBarcodeColor;
use App\Models\TblPurcBarcodeSize;
use App\Models\TblPurcBarcodeTags;
use App\Models\TblPurcProductBarcodeDtl;
use App\Models\TblPurcProductBarcodePurchRate;
use App\Models\TblPurcProductBarcodeSaleRate;
use App\Models\TblPurcProductFOC;
use App\Models\TblPurcProductItemTag;
use App\Models\TblPurcProductSpecificationTag;
use App\Models\TblPurcProductType;
use App\Models\TblPurcRateCategory;
use App\Models\TblPurcSupplier;
use App\Models\TblPurcWarrentyPeriod;
use App\Models\TblSoftBranch;
use App\Models\TblDefiCountry;
use App\Models\TblPurcBrand;
use App\Models\TblPurcGroupItem;
use App\Models\TblPurcProduct;
use App\Models\TblPurcProductBarcode;
use App\Models\TblPurcProductLife;
use App\Models\TblPurcPacking;
use App\Models\TblPurcManufacturer;
use App\Models\TblDefiUom;
use App\Models\TblSoftProductTypeGroup;
use App\Models\TblSoftUserActivityLog;
use App\Models\ViewPurcGroupItem;
use App\Models\ViewInveDisplayLocation;
use App\Models\User;
use Faker\Generator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Session;
use Image;
use App\Models\TblPurcDemandDtl;
use App\Models\TblPurcDemandApprovalDtl;
use App\Models\TblPurcLpoDtl;
use App\Models\TblPurcPurchaseOrderDtl;
use App\Models\TblPurcGrnDtl;
use App\Models\TblSaleSalesOrderDtl;
use App\Models\TblSaleSalesContractDtl;
use App\Models\TblSaleSalesDtl;
use App\Models\TblSaleBarcodePriceTagDtl;
use App\Models\TblInveStockDtl;
use App\Models\TblInveItemFormulationDtl;

use Browser;
// db and Validator
use Illuminate\Validation\Rule;
use Validator;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Schema;

use Importer;

class ProductController extends Controller
{

    public static $page_title = 'Product';
    public static $redirect_url = 'product';
    public static $menu_dtl_id = '6';

    public function __construct()
    {
        $getStaticPrefix = Utilities::getStaticPrefix(self::$redirect_url);
        $this->current_path = $getStaticPrefix['path'];
        $this->page_form_edit = '/'.self::$redirect_url.'/edit';
        $this->page_view = '/'.self::$redirect_url.'/view';
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $data = [];
        if($request->ajax()){

            $dataSql = TblPurcProduct::where('product_id', '<>', '0');

            $sortDirection  = ($request->has('sort.sort') && $request->filled('sort.sort'))? $request->input('sort.sort') : 'asc';
            $sortField  = ($request->has('sort.field') && $request->filled('sort.field'))? $request->input('sort.field') : 'sort_no';
            $meta    = [];
            $page  = ($request->has('pagination.page') && $request->filled('pagination.page'))? $request->input('pagination.page') : 1;
            $perpage  = ($request->has('pagination.perpage') && $request->filled('pagination.perpage'))? $request->input('pagination.perpage') : -1;

            $total  = $dataSql->count();
            // $perpage 0; get all data
            if ($perpage > 0) {
                $pages  = ceil($total / $perpage); // calculate total pages
                $page   = max($page, 1); // get 1 page when $_REQUEST['page'] <= 0
                $page   = min($page, $pages); // get last page when $_REQUEST['page'] > $totalPages
                $offset = ($page - 1) * $perpage;
                if ($offset < 0) {
                    $offset = 0;
                }

                //$data = array_slice($data, $offset, $perpage, true);
            }
            $perpage = 200;
            $entries = $dataSql->orderBy($sortField, $sortDirection)->skip($offset)->take($perpage)->get();

            $meta = [
                'page'    => $page,
                'pages'   => $pages,
                'perpage' => $perpage,
                'total'   => $total
            ];

            $result = [
                'meta' => $meta + [
                        'sort'  => $sortDirection,
                        'field' => $sortField,
                    ],
                'data' => $entries,
            ];
            return response()->json($result);
        }
        return view('purchase.product.listing', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data['page_data'] = [];
        $data['page_data']['title'] = self::$page_title;
        $data['page_data']['path_index'] = $this->prefixIndexPage.self::$redirect_url;;
        $data['page_data']['create'] = '/'.self::$redirect_url.$this->prefixCreatePage;
        $data['permission'] = self::$menu_dtl_id.'-create';
        $data['page_data']['type'] = 'new';
        $doc_data = [
            'biz_type'          => 'business',
            'model'             => 'TblPurcProduct',
            'code_field'        => 'product_code',
            'code_prefix'       => strtoupper('p')
        ];
        $data['document_code'] = Utilities::documentCode($doc_data);
        $data['group_item'] = ViewPurcGroupItem::orderBy('group_item_name_string')->where(Utilities::currentBC())->get();
        $data['item_type'] = TblSoftProductTypeGroup::where('product_type_group_entry_status',1)->where(Utilities::currentBC())->get();
        $data['country'] = TblDefiCountry::where(Utilities::currentBC())->get();
        $data['branch'] = TblSoftBranch::where('branch_active_status',1)->where(Utilities::currentBC())->get();
        $data['brand'] = TblPurcBrand::where('brand_entry_status',1)->where(Utilities::currentBC())->get();
        $data['manufacturer'] = TblPurcManufacturer::where('manufacturer_entry_status',1)->where(Utilities::currentBC())->get();
        $data['uom'] = TblDefiUom::where('uom_entry_status',1)->where(Utilities::currentBC())->get();
        $data['packing'] = TblPurcPacking::where('packing_entry_status',1)->where(Utilities::currentBC())->get();
        $data['warranty_period'] = TblPurcWarrentyPeriod::where('warrenty_period_entry_status',1)->where(Utilities::currentBC())->get();
        $data['rate_category'] = TblPurcRateCategory::where('rate_category_entry_status',1)->where(Utilities::currentBC())->get();
        $data['users'] = User::where('user_type','erp')->where('user_entry_status',1)->where(Utilities::currentBC())->get();
        $data['specific'] = TblDefiTags::where('tags_type',config('constants.tags_type.Specification'))->where(Utilities::currentBC())->where('tags_entry_status',1)->get();
        $data['item'] = TblDefiTags::where('tags_type',config('constants.tags_type.Item'))->where('tags_entry_status',1)->where(Utilities::currentBC())->get();
        $data['color'] = TblDefiColor::where('color_entry_status',1)->where(Utilities::currentBC())->get();
        $data['size'] = TblDefiSize::where('size_entry_status',1)->where(Utilities::currentBC())->get();
        $data['display_location'] = ViewInveDisplayLocation::orderBy('display_location_name_string')->get();

      //  dd($data['display_location']->toArray());
        $data['suppliers'] = TblPurcSupplier::where('supplier_entry_status',1)->where(Utilities::currentBC())->orderBy('supplier_name')->get();
        $data['product_type'] = TblPurcProductType::where('product_type_entry_status',1)->where(Utilities::currentBC())->orderBy('product_type_name')->get();
        //$data['variant'] = TblDefiTags::where('tags_type',config('constants.tags_type.Variant'))->where('tags_entry_status',1)->get();
        $arr = [
            'biz_type' => 'business',
            'code' => $data['document_code'],
            'link' => $data['page_data']['create'],
            'table_name' => 'tbl_purc_product',
            'col_id' => 'product_id',
            'col_code' => 'product_code',
        ];
        $data['switch_entry'] = $this->switchEntry($arr);
        return view('purchase.product.add', compact('data'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //dd($request->toArray());
        $data = [];
        $validator = Validator::make($request->all(), [
            'product_name' => 'required|max:100'
        ]);
        if ($validator->fails()) {
            $data['validator_errors'] = $validator->errors();
            return $this->jsonErrorResponse($data, trans('message.required_fields'), 422);
        }
        DB::beginTransaction();
        try {
            $id = Utilities::uuid();
            $product = new TblPurcProduct();
            $product->product_id = $id;
            $doc_data = [
                'biz_type'          => 'business',
                'model'             => 'TblPurcProduct',
                'code_field'        => 'product_code',
                'code_prefix'       => strtoupper('p')
            ];
            $form_id = $product->product_id;
            $product->product_code = Utilities::documentCode($doc_data);
            $product->product_name = $request->product_name;
            $product->product_short_name = isset($request->product_short_name)?$request->product_short_name:'';
            $product->product_arabic_name = isset($request->product_arabic_name)?$request->product_arabic_name:'';
            $product->product_arabic_short_name = isset($request->product_arabic_short_name)?$request->product_arabic_short_name:'';
            $product->product_entry_status = isset($request->product_entry_status)?'1':'0';
            $product->product_can_sale = isset($request->product_can_sale)?'1':'0';
            $product->group_item_id = isset($request->product_control_group)?$request->product_control_group:'';
            $product->product_item_type = isset($request->product_item_type)?$request->product_item_type:'';
            $product->product_manufacturer_id = isset($request->product_manufacturer)?$request->product_manufacturer:'';
            $product->country_id = isset($request->product_country)?$request->product_country:'';
            $product->product_brand_id = isset($request->product_brand_name)?$request->product_brand_name:'';
            $product->product_demand_active_status = isset($request->product_demand_active_status)?'1':'0';
            $product->product_warranty_status = isset($request->product_warranty_status)?'1':'0';
            $product->product_warranty_period_id = isset($request->product_warranty_period)?$request->product_warranty_period:'';
            $product->product_warranty_period_mode = isset($request->product_warranty_mode)?$request->product_warranty_mode:'';
            $product->product_perishable = isset($request->product_perishable)?'1':'0';
            $product->product_tracing_days = isset($request->product_tracing_days)?$request->product_tracing_days:'';
            $product->product_batch_req = isset($request->product_batch_no_required)?'1':'0';
            $product->product_expiry_return_allow = isset($request->product_expiry_return_allow)?'1':'0';
            $product->product_damage_return_allow = isset($request->product_damage_return_allow)?'1':'0';
            $product->product_expiry_required = isset($request->product_expiry_required)?'1':'0';
            if (Schema::hasColumn('tbl_purc_product','product_barcode_length_calc')) {
                $product->product_barcode_length_calc = isset($request->product_barcode_length_calc)?1:"";
            }
            $product->product_expiry_base = isset($request->product_expiry_base_on)?$request->product_expiry_base_on:'';
            $product->product_shelf_life_minimum = isset($request->product_shelf_life_minimum)?$request->product_shelf_life_minimum:'';
            $product->product_remarks = isset($request->product_remarks)?$request->product_remarks:'';
            $product->business_id = auth()->user()->business_id;
            $product->company_id = auth()->user()->company_id;
            $product->branch_id = 1;
            $product->product_user_id = auth()->user()->id;
            $product->supplier_id = isset($request->supplier_id)?$request->supplier_id:'';
            $product->product_type_id = isset($request->product_type)?$request->product_type:'';
            $product->save();
            if(isset($request->foc)){
                foreach ($request->foc as $k=>$foc){
                    $foc_qty = new TblPurcProductFOC();
                    $foc_qty->product_foc_id = Utilities::uuid();
                    $foc_qty->sr_no = $k++;
                    $foc_qty->product_id = $product->product_id;
                    $foc_qty->supplier_id = $foc['supplier_id'];
                    $foc_qty->product_foc_purc_qty = $foc['qty'];
                    $foc_qty->product_foc_foc_qty = $foc['foc_qty'];
                    $foc_qty->save();
                }
            }
            if(isset($request->product_specification_tags)){
                foreach ($request->product_specification_tags as $specification_tag){
                    $tag = TblDefiTags::where('tags_id','LIKE',$specification_tag)->where(Utilities::currentBC())->exists();
                    if(!$tag){
                        $specification_tag = $this->CreateTag($specification_tag,config('constants.tags_type.Specification'));
                    }
                    $SpecificationTag = new TblPurcProductSpecificationTag();
                    $SpecificationTag->specification_tag_id = Utilities::uuid();
                    $SpecificationTag->tag_id = $specification_tag;
                    $SpecificationTag->product_id = $product->product_id;
                    $SpecificationTag->specification_tag_entry_status = 1;
                    $SpecificationTag->business_id = auth()->user()->business_id;
                    $SpecificationTag->company_id = auth()->user()->company_id;
                    $SpecificationTag->branch_id = auth()->user()->branch_id;
                    $SpecificationTag->specification_tag_user_id = auth()->user()->id;
                    $SpecificationTag->save();
                }
            }
            if(isset($request->product_item_tags)){
                foreach ($request->product_item_tags as $item_tag){
                    $tag = TblDefiTags::where('tags_id','LIKE',$item_tag)->where(Utilities::currentBC())->exists();
                    if(!$tag){
                        $item_tag = $this->CreateTag($item_tag,config('constants.tags_type.Item'));
                    }
                    $ItemTag = new TblPurcProductItemTag();
                    $ItemTag->item_tag_id = Utilities::uuid();
                    $ItemTag->tag_id = $item_tag;
                    $ItemTag->product_id = $product->product_id;
                    $ItemTag->item_tag_entry_status = 1;
                    $ItemTag->business_id = auth()->user()->business_id;
                    $ItemTag->company_id = auth()->user()->company_id;
                    $ItemTag->branch_id = auth()->user()->branch_id;
                    $ItemTag->item_tag_user_id = auth()->user()->id;
                    $ItemTag->save();
                }
            }
            // Product Life:
            if(isset($request->pd)){
                foreach ($request->pd as $product_life){
                    if(!empty($product_life['country']) && !empty($product_life['period_type']) && !empty($product_life['period'])){
                        $productLife = new TblPurcProductLife();
                        $productLife->product_life_id = Utilities::uuid();
                        $productLife->product_id = $product->product_id;
                        $productLife->country_id = $product_life['country'];
                        $productLife->product_life_period_type = $product_life['period_type'];
                        $productLife->product_life_period = $product_life['period'];
                        $productLife->save();
                    }
                }
            }
            if(isset($request->product_barcode_data)){
                $z = 0;
                foreach ($request->product_barcode_data as $index => $barcode_data){
                    $z++;
                    $exits = TblPurcProductBarcode::where('product_barcode_barcode','like',$barcode_data['v_product_barcode'])->exists();
                    if ($exits) {
                        $uom = TblDefiUom::where('uom_id',$barcode_data['uom_packing_uom'])->first();
                        $messg = "Barcode: ".$barcode_data['v_product_barcode']."</br>";
                        $messg .= "UOM: ".$uom->uom_name."</br>";
                        $messg .= "Packing: ".$barcode_data['product_barcode_packing']."</br>";
                        $messg .= " already exists.";
                        return $this->jsonErrorResponse($data, $messg , 200);
                    }
                    $barcode = new TblPurcProductBarcode();
                    $barcode->product_id = $product->product_id;
                    $barcode->product_barcode_id = Utilities::uuid();
                    $barcode->product_barcode_barcode = trim($barcode_data['v_product_barcode']);
                    $barcode->product_barcode_entry_status = 1;
                    $barcode->product_barcode_sr_no = $z;
                  //  $barcode->product_barcode_minimum_profit = $barcode_data['barcode_minimum_profit_margin'];
                    $barcode->product_barcode_purchase_rate = isset($barcode_data['barcode_rate_purchase_rate'])?$barcode_data['barcode_rate_purchase_rate']:'';
                   // $barcode->product_barcode_purchase_rate_base = $barcode_data['barcode_rate_purchase_rate_base'];
                    //$barcode->product_barcode_purchase_rate_type = $barcode_data['barcode_rate_purchase_rate_type'];
                   // $barcode->product_barcode_cost_rate = $barcode_data['product_barcode_cost_rate'];
                    $barcode->uom_id = $barcode_data["uom_packing_uom"];
                    if($index == 0){
                        $barcode->product_barcode_packing = 1;
                    }else{
                        $barcode->product_barcode_packing = $barcode_data["product_barcode_packing"];
                    }
                    $barcode->product_barcode_variant = $barcode_data["uom_packing_other_tag"];
                    $barcode->product_barcode_weight_apply = isset($barcode_data["product_barcode_weight_apply"])?1:"";
                    if(session()->get('base_barcode') == null && session()->get('base_barcode') == 0){
                        $base_barcode = isset($barcode_data['base_barcode'])?1:0;
                        session()->put('base_barcode',$base_barcode);
                        $barcode->base_barcode = isset($barcode_data['base_barcode'])?1:0;
                    }else{
                        $barcode->base_barcode = 0;
                    }
                    $barcode->product_barcode_user_id = auth()->user()->id;
                    if(isset($barcode_data['product_image']))
                    {
                        $folder = '/products/';
                        if (! File::exists($folder)) {
                            File::makeDirectory($folder, 0775, true,true);
                        }
                        //  $image = $barcode_data->file('product_image');
                        $filename = time() .$z. '.' . $barcode_data['product_image']->getClientOriginalExtension();
                        $path = public_path($folder . $filename);
                        Image::make($barcode_data['product_image']->getRealPath())->resize(200, 200)->save($path);
                        $barcode->product_image_url = isset($filename)?$filename:'';
                    }
                    $barcode->save();
                    if(isset($barcode_data['uom_packing_color_tag'])){
                        foreach ($barcode_data['uom_packing_color_tag'] as $color_tag){
                            $tag = TblDefiColor::where('color_id','LIKE',$color_tag)->where(Utilities::currentBC())->exists();
                            if(!$tag){
                                $color_tag = $this->CreateColorTag($color_tag,config('constants.tags_type.Color'));
                            }
                            $ColorTag = new TblPurcBarcodeColor();
                            $ColorTag->barcode_color_id = Utilities::uuid();
                            $ColorTag->color_id = $color_tag;
                            $ColorTag->product_barcode_id = $barcode->product_barcode_id;
                            $ColorTag->barcode_color_entry_status = 1;
                            $ColorTag->business_id = auth()->user()->business_id;
                            $ColorTag->company_id = auth()->user()->company_id;
                            $ColorTag->branch_id = auth()->user()->branch_id;
                            $ColorTag->barcode_color_user_id = auth()->user()->id;
                            $ColorTag->save();
                        }
                    }
                    if(isset($barcode_data['uom_packing_size_tag'])){
                        foreach ($barcode_data['uom_packing_size_tag'] as $size_tag){
                            $tag = TblDefiSize::where('size_id','LIKE',$size_tag)->where(Utilities::currentBC())->exists();
                            if(!$tag){
                                $size_tag = $this->CreateSizeTag($size_tag,config('constants.tags_type.Size'));
                            }
                            $SizeTag = new TblPurcBarcodeSize();
                            $SizeTag->barcode_size_id = Utilities::uuid();
                            $SizeTag->size_id = $size_tag;
                            $SizeTag->product_barcode_id = $barcode->product_barcode_id;
                            $SizeTag->barcode_size_entry_status = 1;
                            $SizeTag->business_id = auth()->user()->business_id;
                            $SizeTag->company_id = auth()->user()->company_id;
                            $SizeTag->branch_id = auth()->user()->branch_id;
                            $SizeTag->barcode_size_user_id = auth()->user()->id;
                            $SizeTag->save();
                        }
                    }
                    /*if(isset($barcode_data['uom_packing_other_tag'])){
                        foreach ($barcode_data['uom_packing_other_tag'] as $other_tag){
                            $tag = TblDefiTags::where('tags_id','LIKE',$other_tag)->exists();
                            if(!$tag){
                                $other_tag = $this->CreateTag($other_tag,config('constants.tags_type.Variant'));
                            }
                            $VariantTag = new TblPurcBarcodeTags();
                            $VariantTag->barcode_tags_id = Utilities::uuid();
                            $VariantTag->tags_id = $other_tag;
                            $VariantTag->product_barcode_id = $barcode->product_barcode_id;
                            $VariantTag->barcode_tags_entry_status = 1;
                            $VariantTag->business_id = auth()->user()->business_id;
                            $VariantTag->company_id = auth()->user()->company_id;
                            $VariantTag->branch_id = auth()->user()->branch_id;
                            $VariantTag->barcode_tags_user_id = auth()->user()->id;
                            $VariantTag->save();
                        }
                    }*/
                    $branchCount = TblSoftBranch::where('branch_active_status',1)->where(Utilities::currentBC())->count();
                    for ($i=0;$branchCount>$i; $i++){
                        $rateCategory = TblPurcRateCategory::where('rate_category_entry_status',1)->count();
                        for ($a=0; $rateCategory>$a;$a++){
                            $barcodeSR = new TblPurcProductBarcodeSaleRate();
                            $barcodeSR->product_barcode_sale_rate_id = Utilities::uuid();
                            $barcodeSR->product_barcode_id = $barcode->product_barcode_id;
                            $barcodeSR->branch_id = $barcode_data["rate_branchId_$i"];
                            $barcodeSR->product_category_id = $barcode_data["rate_categoryId_".$i."_".$a];
                            $barcodeSR->product_barcode_sale_rate_rate = $barcode_data["rate_categoryVal_".$i."_".$a];
                            $barcodeSR->save();
                        }
                    }

                    for ($i=0;$branchCount>$i; $i++ ){
                        $BranchDtl = new TblPurcProductBarcodeDtl();
                        $BranchDtl->product_barcode_dtl_id = Utilities::uuid();
                        $BranchDtl->product_barcode_id = $barcode->product_barcode_id;
                        if($barcode_data["branch_id_$i"] == $barcode_data["stock_branch_id_$i"] && $barcode_data["stock_branch_id_$i"] == $barcode_data["tax_branch_id_$i"]){
                            $BranchDtl->branch_id = $barcode_data["branch_id_$i"];
                        }else{
                            $data['branch_error'] = 'Branch Error';
                            return $this->jsonErrorResponse($data, trans('message.error'), 200);
                        }
                        $BranchDtl->product_barcode_stock_limit_neg_stock = isset($barcode_data["stock_limit_neg_stock_$i"][0])?'1':'0';
                        $BranchDtl->product_barcode_stock_limit_reorder_qty = $barcode_data["stock_qty_level_$i"];
                        $BranchDtl->product_barcode_shelf_stock_max_qty = $barcode_data["stock_max_limit_$i"];
                        $BranchDtl->product_barcode_shelf_stock_min_qty = $barcode_data["stock_min_limit_$i"];
                        $BranchDtl->product_barcode_stock_cons_day = $barcode_data["stock_consumption_days_$i"];
                        $BranchDtl->product_barcode_stock_limit_limit_apply = isset($barcode_data["stock_limit_apply_status_$i"][0])?'1':'0';
                        $BranchDtl->product_barcode_stock_limit_status = isset($barcode_data["stock_status_$i"][0])?'1':'0';
                        $BranchDtl->product_barcode_shelf_stock_location = !empty($barcode_data["shelf_stock_location_$i"])?$barcode_data["shelf_stock_location_$i"]:"";
                        $BranchDtl->product_barcode_shelf_stock_sales_man = isset($barcode_data["shelf_stock_salesman_$i"])?$barcode_data["shelf_stock_salesman_$i"]:"";
                        $BranchDtl->product_barcode_stock_limit_max_qty = $barcode_data["shelf_stock_max_qty_$i"];
                        $BranchDtl->product_barcode_stock_limit_min_qty = $barcode_data["shelf_stock_min_qty_$i"];
                        $BranchDtl->product_barcode_tax_value = $barcode_data["tax_tax_value_$i"];
                        $BranchDtl->product_barcode_tax_apply = isset($barcode_data["tax_tax_status_$i"][0])?'1':'0';
                        $BranchDtl->company_id = auth()->user()->company_id;
                        $BranchDtl->business_id = auth()->user()->business_id;
                        $BranchDtl->save();
                    }
                    for ($i=0;$branchCount>$i; $i++ ){
                        $PurchRate = new TblPurcProductBarcodePurchRate();
                        $PurchRate->product_barcode_purch_id = Utilities::uuid();
                        $PurchRate->product_id = $product->product_id;
                        $PurchRate->product_barcode_id = $barcode->product_barcode_id;
                        $PurchRate->product_barcode_barcode = trim($barcode_data['v_product_barcode']);
                        $PurchRate->product_barcode_purchase_rate = $barcode_data["pr_purchase_value_$i"];
                        $PurchRate->product_barcode_cost_rate = $barcode_data["pr_cost_value_$i"];
                        $PurchRate->product_barcode_avg_rate = $barcode_data["pr_avg_value_$i"];
                        $PurchRate->business_id = auth()->user()->business_id;
                        $PurchRate->company_id = auth()->user()->company_id;
                        $PurchRate->branch_id = $barcode_data["pr_branch_id_$i"];
                        $PurchRate->save();
                    }
                }
            }

            $form_data = TblPurcProduct::with('product_life','specification_tags','item_tags','product_foc','product_barcode')->where('product_id',$product->product_id)->where(Utilities::currentBC())->first()->toArray();
            $log = [
                'menu_dtl_id' => self::$menu_dtl_id,
                'document_id' => $product->product_id,
                'document_name' => 'product',
                'activity_form_menu_dtl_id' => self::$menu_dtl_id,
                'activity_form_id' => $product->product_id,
                'activity_form_type' => 'product',
                'action_type' => 'create',
                'form_data' => serialize((object)$form_data),
                'remarks' => 'create product',
            ];
            $this->userFormLogs($log);
            session()->forget('base_barcode');

        }catch (QueryException $e) {
            DB::rollback();
            $data['error'] = $e->getMessage();
            return $this->jsonErrorResponse($data, $e->getMessage(), 200);
        } catch (ModelNotFoundException $e) {
            DB::rollback();
            $data['error'] = $e->getMessage();
            return $this->jsonErrorResponse($data, $e->getMessage(), 200);
        } catch (ValidationException $e) {
             DB::rollback();
            $data['error'] = $e->getMessage();
            return $this->jsonErrorResponse($data, $e->getMessage(), 200);
        } catch (Exception $e) {
             DB::rollback();
            $data['error'] = $e->getMessage();
            return $this->jsonErrorResponse($data, $e->getMessage(), 200);
        }
        DB::commit();
        Session::put('ProLastData', ['product_name' => $product->product_name, 'product_arabic_name' => $product->product_arabic_name, 'product_group'=>$product->group_item_id , 'product_item_type' => $product->product_item_type ]);
        $data['redirect'] = '/'.self::$redirect_url.$this->prefixCreatePage.'/'.$form_id;
        return $this->jsonSuccessResponse($data, trans('message.create'), 200);
    }
    public function CreateTag($name,$type){
        $tag = new TblDefiTags();
        $tag->tags_id = Utilities::uuid();
        $tag->tags_name = $name;
        $tag->tags_type = $type;
        $tag->tags_entry_status = 1;
        $tag->business_id = auth()->user()->business_id;
        $tag->company_id = auth()->user()->company_id;
        $tag->branch_id = auth()->user()->branch_id;
        $tag->tags_user_id = auth()->user()->id;
        $tag->save();
        return $tag->tags_id;
    }
    public function CreateColorTag($name,$type){
        $tag = new TblDefiColor();
        $tag->color_id = Utilities::uuid();
        $tag->color_name = $name;
        $tag->color_entry_status = 1;
        $tag->business_id = auth()->user()->business_id;
        $tag->company_id = auth()->user()->company_id;
        $tag->branch_id = auth()->user()->branch_id;
        $tag->color_user_id = auth()->user()->id;
        $tag->save();
        return $tag->color_id;
    }
    public function CreateSizeTag($name,$type){
        $tag = new TblDefiSize();
        $tag->size_id = Utilities::uuid();
        $tag->size_name = $name;
        $tag->size_entry_status = 1;
        $tag->business_id = auth()->user()->business_id;
        $tag->company_id = auth()->user()->company_id;
        $tag->branch_id = auth()->user()->branch_id;
        $tag->size_user_id = auth()->user()->id;
        $tag->save();
        return $tag->size_id;
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

    public function FormItemtype($id)
    {
        $group_id  = TblPurcGroupItem::where('group_item_id',$id)->where(Utilities::currentBC())->first();
      // dd($group_id->product_type_group_id);
        $item_type = TblSoftProductTypeGroup::where('product_type_group_id',$group_id->product_type_group_id)->where(Utilities::currentBC())->first();
        $data['product_type_group_id'] = isset($group_id->product_type_group_id)?$group_id->product_type_group_id:"";
      //  return response()->json($group_id->product_type_group_id);
        return $this->jsonSuccessResponse($data, trans('message.create'), 200);
    }

    public function FormSaleman($id)
    {
        $users  = User::where('branch_id',$id)->where('user_type','erp')->where('user_entry_status',1)->where(Utilities::currentBC())->get();

        return response()->json($users);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data['page_data'] = [];
        $data['page_data']['title'] = self::$page_title;
        $data['page_data']['path_index'] = $this->prefixIndexPage.self::$redirect_url;
        $data['page_data']['create'] = '/'.self::$redirect_url.$this->prefixCreatePage;
        $data['page_data']['log_print'] = true;
        if($this->current_path == $this->page_view){
            $data['page_data']['type'] = 'view';
            $data['permission'] = self::$menu_dtl_id.'-view';
        }
        if($this->current_path == $this->page_form_edit){
            $data['page_data']['type'] = 'edit';
            $data['permission'] = self::$menu_dtl_id.'-edit';
        }
        if(TblPurcProduct::where('product_id','LIKE',$id)->where(Utilities::currentBC())->exists()){
            $data['current'] = TblPurcProduct::with('product_life','product_barcode','specification_tags','item_tags')->where(Utilities::currentBC())->where('product_id',$id)->first();
            $data['group_item'] = ViewPurcGroupItem::orderBy('group_item_name_string')->where(Utilities::currentBC())->get();
            $data['item_type'] = TblSoftProductTypeGroup::where('product_type_group_entry_status',1)->where(Utilities::currentBC())->get();
            $data['country'] = TblDefiCountry::where(Utilities::currentBC())->get();
            $data['branch'] = TblSoftBranch::where('branch_active_status',1)->where(Utilities::currentBC())->get();
            $data['brand'] = TblPurcBrand::where('brand_entry_status',1)->where(Utilities::currentBC())->get();
            $data['manufacturer'] = TblPurcManufacturer::where('manufacturer_entry_status',1)->where(Utilities::currentBC())->get();
            $data['uom'] = TblDefiUom::where('uom_entry_status',1)->where(Utilities::currentBC())->get();
            $data['packing'] = TblPurcPacking::where('packing_entry_status',1)->where(Utilities::currentBC())->get();
            $data['warranty_period'] = TblPurcWarrentyPeriod::where('warrenty_period_entry_status',1)->where(Utilities::currentBC())->get();
            $data['rate_category'] = TblPurcRateCategory::where('rate_category_entry_status',1)->where(Utilities::currentBC())->get();
            $data['users'] = User::where('user_type','erp')->where('user_entry_status',1)->where(Utilities::currentBC())->get();
            $data['specific'] = TblDefiTags::where('tags_type',config('constants.tags_type.Specification'))->where('tags_entry_status',1)->where(Utilities::currentBC())->get();
            $data['item'] = TblDefiTags::where('tags_type',config('constants.tags_type.Item'))->where('tags_entry_status',1)->where(Utilities::currentBC())->get();
            $data['color'] = TblDefiColor::where('color_entry_status',1)->where(Utilities::currentBC())->get();
            $data['size'] = TblDefiSize::where('size_entry_status',1)->where(Utilities::currentBC())->get();
            $data['display_location'] = ViewInveDisplayLocation::orderBy('display_location_name_string')->get();
            $data['variant'] = TblDefiTags::where('tags_type',config('constants.tags_type.Variant'))->where('tags_entry_status',1)->where(Utilities::currentBC())->get();
            $data['suppliers'] = TblPurcSupplier::where('supplier_entry_status',1)->where(Utilities::currentBC())->orderBy('supplier_name')->get();
            $data['product_type'] = TblPurcProductType::where('product_type_entry_status',1)->where(Utilities::currentBC())->orderBy('product_type_name')->get();
            $data['product_foc'] = TblPurcProductFOC::with('supplier')->where('product_id','LIKE',$id)->orderBy('sr_no')->get();
            $arr = [
                'biz_type' => 'business',
                'code' => $data['current']->product_code,
                'link' => $data['page_data']['create'],
                'table_name' => 'tbl_purc_product',
                'col_id' => 'product_id',
                'col_code' => 'product_code',
            ];
            $data['switch_entry'] = $this->switchEntry($arr);
            return view('purchase.product.edit', compact('data'));
        }else{
            abort('404');
        }
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
        //dd($request->toArray());
        $data = [];
        $validator = Validator::make($request->all(), [
            'product_name' => 'required|max:100'
        ]);
        if ($validator->fails()) {
            $data['validator_errors'] = $validator->errors();
            return $this->jsonErrorResponse($data, trans('message.required_fields'), 422);
        }
        $referer = Utilities::getReferer($request,$id);
        if($referer == $this->page_view){
            return $this->jsonErrorResponse($data, "Cannot update this entry.", 200);
        }
        DB::beginTransaction();
        try{
            if(TblSoftUserActivityLog::where('document_id',$id)->where(Utilities::currentBC())->count() == 0){
                $form_data = TblPurcProduct::with('product_life','specification_tags','item_tags','product_foc','product_barcode')->where('product_id',$id)->where(Utilities::currentBC())->first()->toArray();
                $log = [
                    'menu_dtl_id' => self::$menu_dtl_id,
                    'document_id' => $id,
                    'document_name' => 'product',
                    'activity_form_menu_dtl_id' => self::$menu_dtl_id,
                    'activity_form_id' => $id,
                    'activity_form_type' => 'product',
                    'action_type' => 'before_update',
                    'form_data' => serialize((object)$form_data),
                    'remarks' => 'first time log created.',
                ];
                $this->userFormLogs($log);
            }
            $product = TblPurcProduct::where('product_id',$id)->where(Utilities::currentBC())->first();
            //dd($product->toArray());
            $product->product_name = $request->product_name;
            $product->product_short_name = $request->product_short_name;
            $product->product_arabic_name = $request->product_arabic_name;
            $product->product_arabic_short_name = $request->product_arabic_short_name;
            $product->product_entry_status = isset($request->product_entry_status)?'1':'0';
            $product->product_can_sale = isset($request->product_can_sale)?'1':'0';
            $product->group_item_id = $request->product_control_group;
            $product->product_item_type = $request->product_item_type;
            $product->product_manufacturer_id = $request->product_manufacturer;
            $product->country_id = $request->product_country;
            $product->product_brand_id = $request->product_brand_name;
            $product->product_demand_active_status = isset($request->product_demand_active_status)?'1':'0';
            $product->product_warranty_status = isset($request->product_warranty_status)?'1':'0';
            $product->product_warranty_period_id = $request->product_warranty_period;
            $product->product_warranty_period_mode = $request->product_warranty_mode;
            $product->product_perishable = isset($request->product_perishable)?'1':'0';
            $product->product_tracing_days = $request->product_tracing_days;
            $product->product_batch_req = isset($request->product_batch_no_required)?'1':'0';
            $product->product_expiry_return_allow = isset($request->product_expiry_return_allow)?'1':'0';
            $product->product_damage_return_allow = isset($request->product_damage_return_allow)?'1':'0';
            $product->product_expiry_required = isset($request->product_expiry_required)?'1':'0';
            if (Schema::hasColumn('tbl_purc_product','product_barcode_length_calc')) {
                $product->product_barcode_length_calc = isset($request->product_barcode_length_calc)?1:"";
            }
            $product->product_expiry_base = $request->product_expiry_base_on;
            $product->product_shelf_life_minimum = $request->product_shelf_life_minimum;
            $product->product_remarks = $request->product_remarks;
            $product->business_id = auth()->user()->business_id;
            $product->company_id = auth()->user()->company_id;
            $product->branch_id = 1;
            $product->product_user_id = auth()->user()->id;
            $product->update_id = Utilities::uuid();
            $product->supplier_id = isset($request->supplier_id)?$request->supplier_id:'';
            $product->product_type_id = isset($request->product_type)?$request->product_type:'';
            $product->save();
            TblPurcProductFOC::where('product_id',$product->product_id)->delete();
            if(isset($request->foc)){
                foreach ($request->foc as $k=>$foc){
                    $foc_qty = new TblPurcProductFOC();
                    $foc_qty->product_foc_id = Utilities::uuid();
                    $foc_qty->sr_no = $k++;
                    $foc_qty->product_id = $product->product_id;
                    $foc_qty->supplier_id = $foc['supplier_id'];
                    $foc_qty->product_foc_purc_qty = $foc['qty'];
                    $foc_qty->product_foc_foc_qty = $foc['foc_qty'];
                    $foc_qty->save();
                }
            }
            if(isset($request->product_specification_tags)){
                $dels = TblPurcProductSpecificationTag::where('product_id',$id)->where(Utilities::currentBC())->get();
                foreach ($dels as $del){
                    TblPurcProductSpecificationTag::where('product_id',$del->product_id)->where(Utilities::currentBC())->delete();
                }
                foreach ($request->product_specification_tags as $specification_tag){
                    $tag = TblDefiTags::where('tags_id','LIKE',$specification_tag)->where(Utilities::currentBC())->exists();
                    if(!$tag){
                        $specification_tag = $this->CreateTag($specification_tag,config('constants.tags_type.Specification'));
                    }
                    $SpecificationTag = new TblPurcProductSpecificationTag();
                    $SpecificationTag->specification_tag_id = Utilities::uuid();
                    $SpecificationTag->tag_id = $specification_tag;
                    $SpecificationTag->product_id = $id;
                    $SpecificationTag->specification_tag_entry_status = 1;
                    $SpecificationTag->business_id = auth()->user()->business_id;
                    $SpecificationTag->company_id = auth()->user()->company_id;
                    $SpecificationTag->branch_id = auth()->user()->branch_id;
                    $SpecificationTag->specification_tag_user_id = auth()->user()->id;
                    $SpecificationTag->save();
                }
            }

            if(isset($request->product_item_tags)){
                $dels = TblPurcProductItemTag::where('product_id',$id)->where(Utilities::currentBC())->get();
                foreach ($dels as $del){
                    TblPurcProductItemTag::where('product_id',$del->product_id)->where(Utilities::currentBC())->delete();
                }
                foreach ($request->product_item_tags as $item_tag){
                    $tag = TblDefiTags::where('tags_id','LIKE',$item_tag)->where(Utilities::currentBC())->exists();
                    if(!$tag){
                        $item_tag = $this->CreateTag($item_tag,config('constants.tags_type.Item'));
                    }
                    $ItemTag = new TblPurcProductItemTag();
                    $ItemTag->item_tag_id = Utilities::uuid();
                    $ItemTag->tag_id = $item_tag;
                    $ItemTag->product_id = $id;
                    $ItemTag->item_tag_entry_status = 1;
                    $ItemTag->business_id = auth()->user()->business_id;
                    $ItemTag->company_id = auth()->user()->company_id;
                    $ItemTag->branch_id = auth()->user()->branch_id;
                    $ItemTag->item_tag_user_id = auth()->user()->id;
                    $ItemTag->save();
                }
            }
            // Product Life:
            if(isset($request->pd)){
                $dels = TblPurcProductLife::where('product_id',$id)->get();
                foreach ($dels as $del){
                    TblPurcProductLife::where('product_id',$del->product_id)->delete();
                }
                foreach ($request->pd as $product_life){
                    if(!empty($product_life['country']) && !empty($product_life['period_type']) && !empty($product_life['period'])){
                        $productLife = new TblPurcProductLife();
                        $productLife->product_life_id = Utilities::uuid();
                        $productLife->product_id = $id;
                        $productLife->country_id = $product_life['country'];
                        $productLife->product_life_period_type = $product_life['period_type'];
                        $productLife->product_life_period = $product_life['period'];
                        $productLife->save();
                    }
                }
            }

            if(isset($request->product_barcode_data)){
                TblPurcProductBarcodePurchRate::where('product_id',$product->product_id)->where(Utilities::currentBC())->delete();
                $z=0;
                foreach ($request->product_barcode_data as $barcode_data){
                    $z++;
                    $exits = TblPurcProductBarcode::where('product_barcode_barcode','like', $barcode_data['v_product_barcode'])->where('product_barcode_id', '!=' , $barcode_data['product_barcode_id'])->get();
                    if (count($exits) > 0) {
                        $uom = TblDefiUom::where('uom_id',$barcode_data['uom_packing_uom'])->first();
                        $messg = "Barcode: ".$barcode_data['v_product_barcode']."</br>";
                        $messg .= "UOM: ".$uom->uom_name."</br>";
                        $messg .= "Packing: ".$barcode_data['product_barcode_packing']."</br>";
                        $messg .= " already exists.";
                        return $this->jsonErrorResponse($data, $messg , 200);
                    }
                    if(isset($id) && isset($barcode_data['product_barcode_id'])){
                        $barcode = TblPurcProductBarcode::where('product_barcode_id',$barcode_data['product_barcode_id'])->first();
                    }else{
                        $barcode = new TblPurcProductBarcode();
                        $barcode->product_barcode_id = Utilities::uuid();
                    }
                    $barcode->product_id = $product->product_id;
                    $barcode->product_barcode_barcode = trim($barcode_data['v_product_barcode']);
                    $barcode->product_barcode_entry_status = 1;
                    $barcode->product_barcode_sr_no = $z;
                   // $barcode->product_barcode_minimum_profit = $barcode_data['barcode_minimum_profit_margin'];
                    $barcode->product_barcode_purchase_rate = isset($barcode_data['barcode_rate_purchase_rate'])?$barcode_data['barcode_rate_purchase_rate']:'';
                   // $barcode->product_barcode_purchase_rate_base = $barcode_data['barcode_rate_purchase_rate_base'];
                    //$barcode->product_barcode_purchase_rate_type = $barcode_data['barcode_rate_purchase_rate_type'];
                   // $barcode->product_barcode_cost_rate = $barcode_data['product_barcode_cost_rate'];
                    $barcode->uom_id = $barcode_data["uom_packing_uom"];
                    $barcode->product_barcode_packing = $barcode_data["product_barcode_packing"];
                    $barcode->product_barcode_variant = $barcode_data["uom_packing_other_tag"];
                    $barcode->product_barcode_user_id = auth()->user()->id;
                    $barcode->product_barcode_weight_apply = isset($barcode_data["product_barcode_weight_apply"])?1:"";
                    if(session()->get('base_barcode') == null && session()->get('base_barcode') == 0){
                        $base_barcode = isset($barcode_data['base_barcode'])?1:0;
                        session()->put('base_barcode',$base_barcode);
                        $barcode->base_barcode = isset($barcode_data['base_barcode'])?1:0;
                    }else{
                        $barcode->base_barcode = 0;
                    }
                    if(isset($barcode_data['product_image']))
                    {
                      //  $image = $barcode_data->file('product_image');
                        $filename = time() .$z. '.' . $barcode_data['product_image']->getClientOriginalExtension();
                        $path = public_path('/products/' . $filename);
                        Image::make($barcode_data['product_image']->getRealPath())->resize(200, 200)->save($path);
                        $barcode->product_image_url = isset($filename)?$filename:'';
                    }

                    $barcode->save();

                    if(isset($barcode_data['uom_packing_color_tag'])){
                        if(isset($id) && isset($barcode_data['product_barcode_id'])){
                            $dels = TblPurcBarcodeColor::where('product_barcode_id',$barcode_data['product_barcode_id'])->where(Utilities::currentBC())->get();
                            foreach ($dels as $del){
                                TblPurcBarcodeColor::where('product_barcode_id',$del->product_barcode_id)->where(Utilities::currentBC())->delete();
                            }
                            $product_barcode_id = $barcode_data['product_barcode_id'];
                        }else{
                            $product_barcode_id = $barcode->product_barcode_id;
                        }

                        foreach ($barcode_data['uom_packing_color_tag'] as $color_tag){
                            $tag = TblDefiColor::where('color_id','LIKE',$color_tag)->exists();
                            if(!$tag){
                                $color_tag = $this->CreateColorTag($color_tag,config('constants.tags_type.Color'));
                            }

                            $ColorTag = new TblPurcBarcodeColor();
                            $ColorTag->barcode_color_id = Utilities::uuid();
                            $ColorTag->color_id = $color_tag;
                            $ColorTag->product_barcode_id = $product_barcode_id;
                            $ColorTag->barcode_color_entry_status = 1;
                            $ColorTag->business_id = auth()->user()->business_id;
                            $ColorTag->company_id = auth()->user()->company_id;
                            $ColorTag->branch_id = auth()->user()->branch_id;
                            $ColorTag->barcode_color_user_id = auth()->user()->id;
                            $ColorTag->save();
                        }
                    }
                    if(isset($barcode_data['uom_packing_size_tag'])){
                        if(isset($id) && isset($barcode_data['product_barcode_id'])){
                            $dels = TblPurcBarcodeSize::where('product_barcode_id',$barcode_data['product_barcode_id'])->where(Utilities::currentBC())->get();
                            foreach ($dels as $del){
                                TblPurcBarcodeSize::where('product_barcode_id',$del->product_barcode_id)->where(Utilities::currentBC())->delete();
                            }
                            $product_barcode_id = $barcode_data['product_barcode_id'];
                        }else{
                            $product_barcode_id = $barcode->product_barcode_id;
                        }

                        foreach ($barcode_data['uom_packing_size_tag'] as $size_tag){
                            $tag = TblDefiSize::where('size_id','LIKE',$size_tag)->where(Utilities::currentBC())->exists();
                            if(!$tag){
                                $size_tag = $this->CreateSizeTag($size_tag,config('constants.tags_type.Size'));
                            }
                            $SizeTag = new TblPurcBarcodeSize();
                            $SizeTag->barcode_size_id = Utilities::uuid();
                            $SizeTag->size_id = $size_tag;
                            $SizeTag->product_barcode_id = $product_barcode_id;
                            $SizeTag->barcode_size_entry_status = 1;
                            $SizeTag->business_id = auth()->user()->business_id;
                            $SizeTag->company_id = auth()->user()->company_id;
                            $SizeTag->branch_id = auth()->user()->branch_id;
                            $SizeTag->barcode_size_user_id = auth()->user()->id;
                            $SizeTag->save();
                        }
                    }
                    /*if(isset($barcode_data['uom_packing_other_tag'])){
                        $dels = TblPurcBarcodeTags::where('product_barcode_id',$barcode_data['product_barcode_id'])->get();
                        foreach ($dels as $del){
                            TblPurcBarcodeTags::where('product_barcode_id',$del->product_barcode_id)->delete();
                        }
                        foreach ($barcode_data['uom_packing_other_tag'] as $other_tag){
                            $tag = TblDefiTags::where('tags_id','LIKE',$other_tag)->exists();
                            if(!$tag){
                                $other_tag = $this->CreateTag($other_tag,config('constants.tags_type.Variant'));
                            }
                            $VariantTag = new TblPurcBarcodeTags();
                            $VariantTag->barcode_tags_id = Utilities::uuid();
                            $VariantTag->tags_id = $other_tag;
                            $VariantTag->product_barcode_id = $barcode_data['product_barcode_id'];
                            $VariantTag->barcode_tags_entry_status = 1;
                            $VariantTag->business_id = auth()->user()->business_id;
                            $VariantTag->company_id = auth()->user()->company_id;
                            $VariantTag->branch_id = auth()->user()->branch_id;
                            $VariantTag->barcode_tags_user_id = auth()->user()->id;
                            $VariantTag->save();
                        }
                    }*/
                    $branchCount = TblSoftBranch::where('branch_active_status',1)->where(Utilities::currentBC())->count();
                    if(isset($id) && isset($barcode_data['product_barcode_id'])){
                        $dels = TblPurcProductBarcodeSaleRate::where('product_barcode_id',$barcode_data['product_barcode_id'])->get();
                        foreach ($dels as $del){
                            TblPurcProductBarcodeSaleRate::where('product_barcode_id',$del->product_barcode_id)->delete();
                        }
                        $product_barcode_id = $barcode_data['product_barcode_id'];
                    }else{
                        $product_barcode_id = $barcode->product_barcode_id;
                    }
                    //dd($request->toArray());
                    for ($i=0;$branchCount>$i; $i++){
                        $rateCategory = TblPurcRateCategory::where('rate_category_entry_status',1)->where(Utilities::currentBC())->count();
                        for ($a=0; $rateCategory>$a;$a++){
                            $barcodeSR = new TblPurcProductBarcodeSaleRate();
                            $barcodeSR->product_barcode_sale_rate_id = Utilities::uuid();
                            $barcodeSR->product_barcode_id = $product_barcode_id;
                            $barcodeSR->branch_id = $barcode_data["rate_branchId_$i"];
                            $barcodeSR->product_category_id = $barcode_data["rate_categoryId_".$i."_".$a];
                            $barcodeSR->product_barcode_sale_rate_rate = $this->addNo($barcode_data["rate_categoryVal_".$i."_".$a]);
                            $barcodeSR->product_barcode_barcode = trim($barcode_data['v_product_barcode']);
                            $barcodeSR->save();
                        }
                    }

                    if(isset($id) && isset($barcode_data['product_barcode_id'])){
                        $dels = TblPurcProductBarcodeDtl::where('product_barcode_id',$barcode_data['product_barcode_id'])->get();
                        foreach ($dels as $del){
                            TblPurcProductBarcodeDtl::where('product_barcode_id',$del->product_barcode_id)->delete();
                        }
                        $product_barcode_id = $barcode_data['product_barcode_id'];
                    }else{
                        $product_barcode_id = $barcode->product_barcode_id;
                    }
                    for ($i=0;$branchCount>$i; $i++ ){
                        $BranchDtl = new TblPurcProductBarcodeDtl();
                        $BranchDtl->product_barcode_dtl_id = Utilities::uuid();
                        $BranchDtl->product_barcode_id = $product_barcode_id;
                        if($barcode_data["branch_id_$i"] == $barcode_data["stock_branch_id_$i"] && $barcode_data["stock_branch_id_$i"] == $barcode_data["tax_branch_id_$i"]){
                            $BranchDtl->branch_id = $barcode_data["branch_id_$i"];
                        }else{
                            $data['branch_error'] = 'Branch Error';
                            return $this->jsonErrorResponse($data, trans('message.error'), 200);
                        }
                        $BranchDtl->product_barcode_stock_limit_neg_stock = isset($barcode_data["stock_limit_neg_stock_$i"][0])?'1':'0';
                        $BranchDtl->product_barcode_stock_limit_reorder_qty = $barcode_data["stock_qty_level_$i"];
                        $BranchDtl->product_barcode_shelf_stock_max_qty = $barcode_data["stock_max_limit_$i"];
                        $BranchDtl->product_barcode_shelf_stock_min_qty = $barcode_data["stock_min_limit_$i"];
                        $BranchDtl->product_barcode_stock_cons_day = $barcode_data["stock_consumption_days_$i"];
                        $BranchDtl->product_barcode_stock_limit_limit_apply = isset($barcode_data["stock_limit_apply_status_$i"][0])?'1':'0';
                        $BranchDtl->product_barcode_stock_limit_status = isset($barcode_data["stock_status_$i"][0])?'1':'0';
                        $BranchDtl->product_barcode_shelf_stock_location = !empty($barcode_data["shelf_stock_location_$i"])?$barcode_data["shelf_stock_location_$i"]:"";
                        $BranchDtl->product_barcode_shelf_stock_sales_man = isset($barcode_data["shelf_stock_salesman_$i"])?$barcode_data["shelf_stock_salesman_$i"]:' ';
                        $BranchDtl->product_barcode_stock_limit_max_qty = $barcode_data["shelf_stock_max_qty_$i"];
                        $BranchDtl->product_barcode_stock_limit_min_qty = $barcode_data["shelf_stock_min_qty_$i"];
                        $BranchDtl->product_barcode_tax_value = $barcode_data["tax_tax_value_$i"];
                        $BranchDtl->product_barcode_tax_apply = isset($barcode_data["tax_tax_status_$i"][0])?'1':'0';
                        $BranchDtl->company_id = auth()->user()->company_id;
                        $BranchDtl->business_id = auth()->user()->business_id;
                        $BranchDtl->save();
                    }
                 //   dd($request->toArray());
                    for ($i=0;$branchCount>$i; $i++ ){
                        $PurchRate = new TblPurcProductBarcodePurchRate();
                        $PurchRate->product_barcode_purch_id = Utilities::uuid();
                        $PurchRate->product_id = $product->product_id;
                        $PurchRate->product_barcode_id = $barcode->product_barcode_id;
                        $PurchRate->product_barcode_barcode = trim($barcode_data['v_product_barcode']);
                        $PurchRate->product_barcode_purchase_rate = $this->addNo($barcode_data["pr_purchase_value_$i"]);
                        $PurchRate->product_barcode_cost_rate = $this->addNo($barcode_data["pr_cost_value_$i"]);
                        $PurchRate->product_barcode_avg_rate = $this->addNo($barcode_data["pr_avg_value_$i"]);
                        $PurchRate->business_id = auth()->user()->business_id;
                        $PurchRate->company_id = auth()->user()->company_id;
                        $PurchRate->branch_id = $barcode_data["pr_branch_id_$i"];
                        $PurchRate->save();
                    }
                }
            }

            $form_data = TblPurcProduct::with('product_life','specification_tags','item_tags','product_foc','product_barcode')->where('product_id',$id)->where(Utilities::currentBC())->first()->toArray();
            $log = [
                'menu_dtl_id' => self::$menu_dtl_id,
                'document_id' => $id,
                'document_name' => 'product',
                'activity_form_menu_dtl_id' => self::$menu_dtl_id,
                'activity_form_id' => $id,
                'activity_form_type' => 'product',
                'action_type' => 'update',
                'form_data' => serialize((object)$form_data),
                'remarks' => 'update product',
            ];
            $this->userFormLogs($log);
            session()->forget('base_barcode');
        }catch (QueryException $e) {
            DB::rollback();
            $data['error'] = $e->getMessage();
            return $this->jsonErrorResponse($data, $e->getMessage(), 200);
        } catch (ModelNotFoundException $e) {
            DB::rollback();
            $data['error'] = $e->getMessage();
            return $this->jsonErrorResponse($data, $e->getMessage(), 200);
        } catch (ValidationException $e) {
            DB::rollback();
            $data['error'] = $e->getMessage();
            return $this->jsonErrorResponse($data, $e->getMessage(), 200);
        } catch (Exception $e) {
            DB::rollback();
            $data['error'] = $e->getMessage();
            return $this->jsonErrorResponse($data, $e->getMessage(), 200);
        }
        DB::commit();
        Session::put('ProLastData', ['product_name' => $product->product_name, 'product_arabic_name' => $product->product_arabic_name, 'product_group'=>$product->group_item_id , 'product_item_type' => $product->product_item_type ]);

        return $this->jsonSuccessResponse($data, trans('message.update'), 200);
    }
    public function BarcodeTagPrintGenerate(Request $request){
        $data = [];
        $data['data_id'] =  $request->data_id;
        $data['product_name'] =  $request->product_name;
        $data['arabic_product_name'] =  $request->product_arabic_name;
        $data['barcode'] =  $request->barcode;
        $data['rate'] =  $request->rate;
        $data['qty'] =  $request->qty;
        session(['data' => $data]);
        $dataJs['url'] = route( 'barcode_tag_print');
        return $this->jsonSuccessResponse($dataJs, trans('message.report_ready'), 200);
    }
    public function BarcodeTagPrint(){
        $data = [];
        $data['barcode'] = Session::get('data');
        $data['permission_create'] = self::$menu_dtl_id.'-create';
        $data['permission_edit'] = self::$menu_dtl_id.'-edit';
        if($data['barcode']['data_id'] == 1){
            return view('purchase.product.barcode_tag_print',compact('data'));
        }else{
            return view('purchase.product.barcode_shelf_tag_print',compact('data'));
        }
    }

    public function CheckBarcode($id)
    {
        $data = [];
        DB::beginTransaction();
        try{
            $tableList = [
                'tbl_inve_item_dtl',
                'tbl_inve_item_formulation',
                'tbl_inve_item_formulation_dtl',
                'tbl_inve_item_stock_adjustment_dtl',
                'tbl_inve_item_stock_opening_dtl',
                'tbl_inve_item_stock_transfer_dtl',
                'tbl_purc_supplier_contract_dtl',
                'tbl_inve_stock',
                'tbl_inve_stock_dtl',
                'tbl_purc_barcode_color',
                'tbl_purc_barcode_size',
                'tbl_purc_barcode_tags',
                'tbl_purc_comparative_quotation_dtl',
                'tbl_purc_demand_approval_dtl',
                'tbl_purc_demand_dtl',
                'tbl_purc_grn_dtl',
                'tbl_purc_lpo_dtl',
                'tbl_purc_lpo_dtl_dtl',
                'tbl_purc_purchase_order_approval_dtl',
                'tbl_purc_purchase_order_dtl',
                'tbl_purc_quotation_dtl',
                'tbl_sale_temp_invoice_dtl',
                'tbl_sale_hold_invoice_dtl',
                'tbl_sale_sales_dtl',
                'tbl_sale_sales_order_dtl',
                'tbl_sale_barcode_price_tag_dtl',
                'tbl_purc_change_rate_dtl',
                'tbl_sale_temp_sales_dtl',
                'tbl_defi_barcode_labels_dtl',
                'tbl_sale_sales_delivery_dtl',
                'tbl_sale_sales_contract_dtl'
            ];

            $checkReturn = false;

            foreach ($tableList as $tbl){
                $check = DB::table($tbl)->where('product_barcode_id',$id)->first();
                if(!empty($check)){
                    $checkReturn = true;
                    break;
                }
            }

            if($checkReturn) {
                $data['check'] = true;
                $msg = "Barcode not delete";

            }else{
                TblPurcProductBarcode::with('sale_rate','purc_rate','barcode_dtl','color','size','variant')->where('product_barcode_id',$id)->delete();
                $data['check'] = false;
                $msg = "Barcode delete";
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
        return $this->jsonSuccessResponse($data, $msg, 200);
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
            $demand = TblPurcDemandDtl::where('product_id',$id)->where(Utilities::currentBCB())->first();
            $demand_aproval = TblPurcDemandApprovalDtl::where('product_id',$id)->where(Utilities::currentBCB())->first();
            $lpo = TblPurcLpoDtl::where('product_id',$id)->where(Utilities::currentBCB())->first();
            $po = TblPurcPurchaseOrderDtl::where('product_id',$id)->where(Utilities::currentBCB())->first();
            $grn = TblPurcGrnDtl::where('product_id',$id)->where(Utilities::currentBCB())->first();
            $SO = TblSaleSalesOrderDtl::where('product_id',$id)->where(Utilities::currentBCB())->first();
            $SC = TblSaleSalesContractDtl::where('product_id',$id)->where(Utilities::currentBCB())->first();
            $SI = TblSaleSalesDtl::where('product_id',$id)->where(Utilities::currentBCB())->first();
            $stock = TblInveStockDtl::where('product_id',$id)->where(Utilities::currentBCB())->first();
            $formulation = TblInveItemFormulationDtl::where('product_id',$id)->where(Utilities::currentBC())->first();
            if($demand == null && $demand_aproval == null && $lpo == null && $po == null && $grn == null && $SO == null && $SC == null && $SI == null && $stock == null && $formulation == null) {

                $product = TblPurcProduct::where('product_id',$id)->where(Utilities::currentBC())->first();
                $product_barcode = TblPurcProductBarcode::where('product_id',$id)->get();
                foreach ($product_barcode as $del){
                    $barcode = TblPurcProductBarcode::where('product_barcode_id',$del->product_barcode_id)->first();
                    $barcode->barcode_dtl()->delete();
                    $barcode->sale_rate()->delete();
                    $barcode->color()->delete();
                    $barcode->size()->delete();
                }
                $product->product_barcode()->delete();
                $product->product_life()->delete();
                $product->specification_tags()->delete();
                $product->item_tags()->delete();
                $product->delete();

            }else{
                return $this->jsonErrorResponse($data, trans('message.not_delete'), 200);
            }

        }catch (QueryException $e) {
            DB::rollback();
            $data['error'] = $e->getMessage();
            return $this->jsonErrorResponse($data, $e->getMessage(), 200);
        } catch (ModelNotFoundException $e) {
            DB::rollback();
            $data['error'] = $e->getMessage();
            return $this->jsonErrorResponse($data, $e->getMessage(), 200);
        } catch (ValidationException $e) {
            DB::rollback();
            $data['error'] = $e->getMessage();
            return $this->jsonErrorResponse($data, $e->getMessage(), 200);
        } catch (Exception $e) {
            DB::rollback();
            $data['error'] = $e->getMessage();
            return $this->jsonErrorResponse($data, $e->getMessage(), 200);
        }
        DB::commit();
        return $this->jsonSuccessResponse($data, trans('message.delete'), 200);
    }

    public function logPrintProduct($id){
        $data = [];
        $data['permission'] = self::$menu_dtl_id.'-edit';
        $data['branch'] = TblSoftBranch::where('branch_active_status',1)->where(Utilities::currentBC())->get();
        $data['rate_category'] = TblPurcRateCategory::where('rate_category_entry_status',1)->where(Utilities::currentBC())->get();
        $data['current'] = TblSoftUserActivityLog::with('user','branch')->where('user_activity_log_id',$id)->first();
        return view('prints_log.product', compact('data'));
    }

    public function importFile()
    {
        $data = [];
        $data['page_data'] = [];
        $data['page_data'] = array_merge($data['page_data'], Utilities::newForm());
        $data['page_data']['action'] = 'Import';
        $data['page_data']['type'] = 'Import';
        $data['page_data']['path_index'] = $this->prefixIndexPage.self::$redirect_url;
        return view('purchase.product.import',compact('data'));
    }

    public function importExcle2(Request $request)
    {

        $data = [];
        $validator = Validator::make($request->all(), [
            'file' => 'required|mimes:xlsx,xls,csv'
        ]);
        if ($validator->fails()) {
            $data['validator_errors'] = $validator->errors();
            return $this->jsonErrorResponse($data, trans('message.required_fields'), 422);
        }

        DB::beginTransaction();
        try {
            $startTime = microtime(true);
            $file = $request->file('file');
            $path = public_path('/upload/');
            $filename = time() . '-' . $file->getClientOriginalName();
            $fileExtension = time() . '-' . $file->getClientOriginalExtension();
            $file->move($path,$filename);

            $excel = Importer::make('Excel');
            $excel->load($path.$filename);
            $collection = $excel->getCollection();

            /*array:32 [
                  0 => "id"
                  1 => "code"
                  2 => "name"
                  3 => "arabic_name"
                  4 => "grp_id"
                  5 => "brand_id"
                  6 => "product_type_id"
                  7 => "photo_name"
                  8 => "stock_qty"

                  9 => "barcode1"
                  10 => "unit1"
                  11 => "packing1"
                  12 => "cost_price1"
                  13 => "sale_price1"
                  14 => "retail_price1"

                  15 => "barcode2"
                  16 => "unit2"
                  17 => "packing2"
                  18 => "cost_price2"
                  19 => "sale_price2"
                  20 => "retail_price2"

                  21 => "barcode3"
                  22 => "unit3"
                  23 => "packing3"
                  24 => "cost_price3"
                  25 => "sale_price3"
                  26 => "retail_price3"

                  27 => "is_active"
                  28 => "is_uploaded"
                  29 => "is_perishable"
                  30 => "is_only_mabela"
                  31 => "is_price_change"
             ]*/
            $c = 0;
            $barcodeSR_id = 551400;
            $barcode_id = 561400;
            for ($row=1; $row < count($collection); $row++){
                $item = $collection[$row];
                $product = new TblPurcProduct();
                $product->product_id =  (int)$item[0];
                $product->product_code = (int)$item[1];
                $product->product_name = $item[2];
                $product->product_arabic_name = $item[3];
                $product->group_item_id = (int)$item[4];
                $product->product_brand_id = (int)$item[5];
                $product->product_item_type = (int)$item[6];
                $product->product_perishable = (int)$item[29];
                $product->product_entry_status = 1;
                $product->business_id = auth()->user()->business_id;
                $product->company_id = auth()->user()->company_id;
                $product->branch_id = auth()->user()->branch_id;
                $product->product_user_id = auth()->user()->id;
                $product->save();
                $c += 1;
                if(!empty($item[9]) && $item[9] != "NULL"){
                    $product_barcode_id_1 = $barcode_id++;
                    $barcode_1 = new TblPurcProductBarcode();
                    $barcode_1->product_id = $product->product_id;
                    $barcode_1->product_barcode_id = $product_barcode_id_1;
                    $barcode_1->product_barcode_barcode = $item[9];
                    $barcode_1->uom_name = (!empty($item[10]) &&  $item[10] != "NULL")?$item[10]:'';
                    $barcode_1->product_barcode_packing = (!empty($item[11]) &&  $item[11] != "NULL")?$item[11]:'';
                    $barcode_1->product_barcode_purchase_rate = (!empty($item[12]) && $item[12] != "NULL")?$item[12]:0.000;
                    $barcode_1->product_barcode_weight_apply = (int)$item[29];
                    $barcode_1->product_barcode_entry_status = 1;
                    $barcode_1->business_id = auth()->user()->business_id;
                    $barcode_1->product_barcode_user_id = auth()->user()->id;
                    $barcode_1->save();
                    //	for Sale Rate
                    $barcodeSR_1_1 = new TblPurcProductBarcodeSaleRate();
                    $barcodeSR_1_1->product_barcode_sale_rate_id = $barcodeSR_id++;
                    $barcodeSR_1_1->product_barcode_id = $product_barcode_id_1;
                    $barcodeSR_1_1->branch_id = 1;
                    $barcodeSR_1_1->product_category_id = 2;
                    $barcodeSR_1_1->product_barcode_sale_rate_rate = (!empty($item[13]) && $item[13] != "NULL")?$item[13]:0.000;
                    $barcodeSR_1_1->product_barcode_barcode = $item[9];
                    $barcodeSR_1_1->save();
                    // for Retail Sale Rate
                    $barcodeSR_1_2 = new TblPurcProductBarcodeSaleRate();
                    $barcodeSR_1_2->product_barcode_sale_rate_id = $barcodeSR_id++;
                    $barcodeSR_1_2->product_barcode_id = $product_barcode_id_1;
                    $barcodeSR_1_2->branch_id = 1;
                    $barcodeSR_1_2->product_category_id = 1;
                    $barcodeSR_1_2->product_barcode_sale_rate_rate = (!empty($item[14]) && $item[14] != "NULL")?$item[14]:0.000;
                    $barcodeSR_1_2->product_barcode_barcode = $item[9];
                    $barcodeSR_1_2->save();
                    $c += 3;
                }
                if(!empty($item[15]) && $item[15] != "NULL"){
                    $product_barcode_id_2 = $barcode_id++;
                    $barcode_2 = new TblPurcProductBarcode();
                    $barcode_2->product_id = $product->product_id;
                    $barcode_2->product_barcode_id = $product_barcode_id_2;
                    $barcode_2->product_barcode_barcode = $item[15];
                    $barcode_2->uom_name = (!empty($item[16]) && $item[16] != "NULL")?$item[16]:'';
                    $barcode_2->product_barcode_packing = (!empty($item[17]) &&  $item[17] != "NULL")?$item[17]:'';
                    $barcode_2->product_barcode_purchase_rate = (!empty($item[18]) && $item[18] != "NULL")?$item[18]:0;
                    $barcode_2->product_barcode_weight_apply = (int)$item[29];
                    $barcode_2->product_barcode_entry_status = 1;
                    $barcode_2->business_id = auth()->user()->business_id;
                    $barcode_2->product_barcode_user_id = auth()->user()->id;
                    $barcode_2->save();
                    //	for Sale Rate
                    $barcodeSR_2_1 = new TblPurcProductBarcodeSaleRate();
                    $barcodeSR_2_1->product_barcode_sale_rate_id = $barcodeSR_id++;
                    $barcodeSR_2_1->product_barcode_id = $product_barcode_id_2;
                    $barcodeSR_2_1->branch_id = 1;
                    $barcodeSR_2_1->product_category_id = 2;
                    $barcodeSR_2_1->product_barcode_sale_rate_rate = (!empty($item[19]) && $item[19] != "NULL")?$item[19]:0;
                    $barcodeSR_2_1->product_barcode_barcode = $item[15];
                    $barcodeSR_2_1->save();
                    // for Retail Sale Rate
                    $barcodeSR_2_2 = new TblPurcProductBarcodeSaleRate();
                    $barcodeSR_2_2->product_barcode_sale_rate_id = $barcodeSR_id++;
                    $barcodeSR_2_2->product_barcode_id = $product_barcode_id_2;
                    $barcodeSR_2_2->branch_id = 1;
                    $barcodeSR_2_2->product_category_id = 1;
                    $barcodeSR_2_2->product_barcode_sale_rate_rate = (!empty($item[20]) && $item[20] != "NULL")?$item[20]:0;
                    $barcodeSR_2_2->product_barcode_barcode = $item[15];
                    $barcodeSR_2_2->save();
                    $c += 3;
                }
                if(!empty($item[21]) && $item[21] != "NULL"){
                    $product_barcode_id_3 = $barcode_id++;
                    $barcode_3 = new TblPurcProductBarcode();
                    $barcode_3->product_id = $product->product_id;
                    $barcode_3->product_barcode_id = $product_barcode_id_3;
                    $barcode_3->product_barcode_barcode = $item[21];
                    $barcode_3->uom_name = (!empty($item[22]) && $item[22] != "NULL")?$item[22]:'';
                    $barcode_3->product_barcode_packing = (!empty($item[23]) &&  $item[23] != "NULL")?$item[23]:'';
                    $barcode_3->product_barcode_purchase_rate = (!empty($item[24]) && $item[24] != "NULL")?$item[24]:0;
                    $barcode_3->product_barcode_weight_apply = (int)$item[29];
                    $barcode_3->product_barcode_entry_status = 1;
                    $barcode_3->business_id = auth()->user()->business_id;
                    $barcode_3->product_barcode_user_id = auth()->user()->id;
                    $barcode_3->save();
                    //	for Sale Rate
                    $barcodeSR_3_1 = new TblPurcProductBarcodeSaleRate();
                    $barcodeSR_3_1->product_barcode_sale_rate_id = $barcodeSR_id++;
                    $barcodeSR_3_1->product_barcode_id = $product_barcode_id_3;
                    $barcodeSR_3_1->branch_id = 1;
                    $barcodeSR_3_1->product_category_id = 2;
                    $barcodeSR_3_1->product_barcode_sale_rate_rate = (!empty($item[25]) && $item[25] != "NULL")?$item[25]:0;
                    $barcodeSR_3_1->product_barcode_barcode = $item[21];
                    $barcodeSR_3_1->save();
                    // for Retail Sale Rate
                    $barcodeSR_3_2 = new TblPurcProductBarcodeSaleRate();
                    $barcodeSR_3_2->product_barcode_sale_rate_id = $barcodeSR_id++;
                    $barcodeSR_3_2->product_barcode_id = $product_barcode_id_3;
                    $barcodeSR_3_2->branch_id = 1;
                    $barcodeSR_3_2->product_category_id = 1;
                    $barcodeSR_3_2->product_barcode_sale_rate_rate = (!empty($item[26]) && $item[26] != "NULL")?$item[26]:0;
                    $barcodeSR_3_2->product_barcode_barcode = $item[21];
                    $barcodeSR_3_2->save();
                    $c += 3;
                }
                $barcodeSR_id = $barcodeSR_id++;
                $barcode_id = $barcode_id++;
            }
            $timeEnd = (microtime(true) - $startTime);
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
        $message = 'Products Successfully Imported <br/>' ;
        $message .= 'Total Time Consume: '.number_format($timeEnd,3).' sec  <br/>';
        $data['loop'] = 'Loop Run '.$c;
        return $this->jsonSuccessResponse($data, $message, 200);

    }

    public function importExcle(Request $request)
    {
        $data = [];
        $validator = Validator::make($request->all(), [
            'file' => 'required|mimes:xlsx,xls,csv'
        ]);
        if ($validator->fails()) {
            $data['validator_errors'] = $validator->errors();
            return $this->jsonErrorResponse($data, trans('message.required_fields'), 422);
        }
        $error_product_id = '';
        $error_product_name = '';
        $startTime = microtime(true);
        $file = $request->file('file');
        $path = public_path('/upload/');
        $filename = $file->getClientOriginalName();
        $fileExtension = time() . '-' . $file->getClientOriginalExtension();
        $file->move($path,$filename);
      //  dd($path.$filename);


        DB::beginTransaction();
        try {
            $excel = Importer::make('Excel');
            $excel->load($path.$filename);
            $collection = $excel->getCollection();
            /*array:32 [
                  0 => "id"
                  1 => "code"
                  2 => "name"
                  3 => "arabic_name"
                  4 => "grp_id"
                  5 => "brand_id"
                  6 => "product_type_id"
                  7 => "photo_name"
                  8 => "stock_qty"

                  9 => "barcode1"
                  10 => "unit1"
                  11 => "packing1"
                  12 => "cost_price1"
                  13 => "sale_price1"
                  14 => "retail_price1"

                  15 => "barcode2"
                  16 => "unit2"
                  17 => "packing2"
                  18 => "cost_price2"
                  19 => "sale_price2"
                  20 => "retail_price2"

                  21 => "barcode3"
                  22 => "unit3"
                  23 => "packing3"
                  24 => "cost_price3"
                  25 => "sale_price3"
                  26 => "retail_price3"

                  27 => "is_active"
                  28 => "is_uploaded"
                  29 => "is_perishable"
                  30 => "is_only_mabela"
                  31 => "is_price_change"
             ]*/
            $pb_count = TblPurcProductBarcode::max('product_barcode_id');
            $barcode_id = ($pb_count==0)?100000000:$pb_count;

            $pbsr_count = TblPurcProductBarcodeSaleRate::max('product_barcode_sale_rate_id');
            $barcodeSR_id = ($pbsr_count==0)?600000000:$pbsr_count;

            $c = 0;
            for ($row=1; $row < count($collection); $row++){
                $item = $collection[$row];
                $error_product_id = $item[0];
                $error_product_name = $item[2];
                $weight_apply = (!empty($item[29]) && $item[29] != "NULL")?$item[29]:0;
                if(TblPurcProduct::where('product_id','like',$item[0])->exists()){
                    $product = TblPurcProduct::where('product_id','like',$item[0])->first();
                    $product_id = $product->product_id;
                }else{
                    $product = TblPurcProduct::create([
                        'product_id' => (int)$item[0],
                        'product_code' => (int)$item[1],
                        'product_name' => $item[2],
                        'product_arabic_name' => $item[3],
                        'group_item_id' => (int)$item[4],
                        'product_brand_id' => (int)$item[5],
                        'product_item_type' => (int)$item[6],
                        'product_perishable' => $weight_apply,
                        'product_can_sale' => 1,
                        'product_entry_status' => 1,
                        'business_id' => 1,
                        'company_id' => 1,
                        'branch_id' => 1,
                        'product_user_id' => 91,
                    ]);
                    $product_id = $product->product_id;
                }
                $c += 1;
                // barcode 1
                if(!empty($item[9]) && $item[9] != "NULL"){
                    $barcode_barcode = $item[9];
                    $uom_name = $item[10];
                    $packing = $item[11];
                    $purchase_rate = $item[12];
                    $category_1_sale_rate = $item[13]; // sale_price
                    $category_2_retail_rate = $item[14]; // retail_price
                    $barcode_id = $barcode_id+1;
                    $barcodeSR_id = $barcodeSR_id+2;
                    $this->insertBarcodeAndRates($product_id,$barcode_id,$barcodeSR_id,$barcode_barcode,$uom_name,$packing,$purchase_rate,$weight_apply,$category_1_sale_rate,$category_2_retail_rate);
                    $c += 3;
                }
                // barcode 2
                if(!empty($item[15]) && $item[15] != "NULL"){
                    $barcode_barcode = $item[15];
                    $uom_name = $item[16];
                    $packing = $item[17];
                    $purchase_rate = $item[18];
                    $category_1_sale_rate = $item[19]; // sale_price
                    $category_2_retail_rate = $item[20]; // retail_price
                    $barcode_id = $barcode_id+1;
                    $barcodeSR_id = $barcodeSR_id+2;
                    $this->insertBarcodeAndRates($product_id,$barcode_id,$barcodeSR_id,$barcode_barcode,$uom_name,$packing,$purchase_rate,$weight_apply,$category_1_sale_rate,$category_2_retail_rate);
                    $c += 3;
                }
                // barcode 3
                if(!empty($item[21]) && $item[21] != "NULL"){
                    $barcode_barcode = $item[21];
                    $uom_name = $item[22];
                    $packing = $item[23];
                    $purchase_rate = $item[24];
                    $category_1_sale_rate = $item[25]; // sale_price
                    $category_2_retail_rate = $item[26]; // retail_price
                    $barcode_id = $barcode_id+1;
                    $barcodeSR_id = $barcodeSR_id+2;
                    $this->insertBarcodeAndRates($product_id,$barcode_id,$barcodeSR_id,$barcode_barcode,$uom_name,$packing,$purchase_rate,$weight_apply,$category_1_sale_rate,$category_2_retail_rate);
                    $c += 3;
                }
                // barcode 4
                if(!empty($item[32]) && $item[32] != "NULL"){
                    $barcode_barcode = $item[32];
                    $uom_name = $item[33];
                    $packing = $item[34];
                    $purchase_rate = $item[35];
                    $category_1_sale_rate = $item[36]; // sale_price
                    $category_2_retail_rate = $item[37]; // retail_price
                    $barcode_id = $barcode_id+1;
                    $barcodeSR_id = $barcodeSR_id+2;
                    $this->insertBarcodeAndRates($product_id,$barcode_id,$barcodeSR_id,$barcode_barcode,$uom_name,$packing,$purchase_rate,$weight_apply,$category_1_sale_rate,$category_2_retail_rate);
                    $c += 3;
                }
                // barcode 5
                if(!empty($item[38]) && $item[38] != "NULL"){
                    $barcode_barcode = $item[38];
                    $uom_name = $item[39];
                    $packing = $item[40];
                    $purchase_rate = $item[41];
                    $category_1_sale_rate = $item[42]; // sale_price
                    $category_2_retail_rate = $item[43]; // retail_price
                    $barcode_id = $barcode_id+1;
                    $barcodeSR_id = $barcodeSR_id+2;
                    $this->insertBarcodeAndRates($product_id,$barcode_id,$barcodeSR_id,$barcode_barcode,$uom_name,$packing,$purchase_rate,$weight_apply,$category_1_sale_rate,$category_2_retail_rate);
                    $c += 3;
                }
                // barcode 6
                if(!empty($item[44]) && $item[44] != "NULL"){
                    $barcode_barcode = $item[44];
                    $uom_name = $item[45];
                    $packing = $item[46];
                    $purchase_rate = $item[47];
                    $category_1_sale_rate = $item[48]; // sale_price
                    $category_2_retail_rate = $item[49]; // retail_price
                    $barcode_id = $barcode_id+1;
                    $barcodeSR_id = $barcodeSR_id+2;
                    $this->insertBarcodeAndRates($product_id,$barcode_id,$barcodeSR_id,$barcode_barcode,$uom_name,$packing,$purchase_rate,$weight_apply,$category_1_sale_rate,$category_2_retail_rate);
                    $c += 3;
                }
                // barcode 7
                if(!empty($item[50]) && $item[50] != "NULL"){
                    $barcode_barcode = $item[50];
                    $uom_name = $item[51];
                    $packing = $item[52];
                    $purchase_rate = $item[53];
                    $category_1_sale_rate = $item[54]; // sale_price
                    $category_2_retail_rate = $item[55]; // retail_price
                    $barcode_id = $barcode_id+1;
                    $barcodeSR_id = $barcodeSR_id+2;
                    $this->insertBarcodeAndRates($product_id,$barcode_id,$barcodeSR_id,$barcode_barcode,$uom_name,$packing,$purchase_rate,$weight_apply,$category_1_sale_rate,$category_2_retail_rate);
                    $c += 3;
                }
                // barcode 8
                if(!empty($item[56]) && $item[56] != "NULL"){
                    $barcode_barcode = $item[56];
                    $uom_name = $item[57];
                    $packing = $item[58];
                    $purchase_rate = $item[59];
                    $category_1_sale_rate = $item[60]; // sale_price
                    $category_2_retail_rate = $item[61]; // retail_price
                    $barcode_id = $barcode_id+1;
                    $barcodeSR_id = $barcodeSR_id+2;
                    $this->insertBarcodeAndRates($product_id,$barcode_id,$barcodeSR_id,$barcode_barcode,$uom_name,$packing,$purchase_rate,$weight_apply,$category_1_sale_rate,$category_2_retail_rate);
                    $c += 3;
                }
                // barcode 9
                if(!empty($item[62]) && $item[62] != "NULL"){
                    $barcode_barcode = $item[62];
                    $uom_name = $item[63];
                    $packing = $item[64];
                    $purchase_rate = $item[65];
                    $category_1_sale_rate = $item[66]; // sale_price
                    $category_2_retail_rate = $item[67]; // retail_price
                    $barcode_id = $barcode_id+1;
                    $barcodeSR_id = $barcodeSR_id+2;
                    $this->insertBarcodeAndRates($product_id,$barcode_id,$barcodeSR_id,$barcode_barcode,$uom_name,$packing,$purchase_rate,$weight_apply,$category_1_sale_rate,$category_2_retail_rate);
                    $c += 3;
                }
                // barcode 10
                if(!empty($item[68]) && $item[68] != "NULL"){
                    $barcode_barcode = $item[68];
                    $uom_name = $item[69];
                    $packing = $item[70];
                    $purchase_rate = $item[71];
                    $category_1_sale_rate = $item[72]; // sale_price
                    $category_2_retail_rate = $item[73]; // retail_price
                    $barcode_id = $barcode_id+1;
                    $barcodeSR_id = $barcodeSR_id+2;
                    $this->insertBarcodeAndRates($product_id,$barcode_id,$barcodeSR_id,$barcode_barcode,$uom_name,$packing,$purchase_rate,$weight_apply,$category_1_sale_rate,$category_2_retail_rate);
                    $c += 3;
                }
                // barcode 11
                if(!empty($item[74]) && $item[74] != "NULL"){
                    $barcode_barcode = $item[74];
                    $uom_name = $item[75];
                    $packing = $item[76];
                    $purchase_rate = $item[77];
                    $category_1_sale_rate = $item[78]; // sale_price
                    $category_2_retail_rate = $item[79]; // retail_price
                    $barcode_id = $barcode_id+1;
                    $barcodeSR_id = $barcodeSR_id+2;
                    $this->insertBarcodeAndRates($product_id,$barcode_id,$barcodeSR_id,$barcode_barcode,$uom_name,$packing,$purchase_rate,$weight_apply,$category_1_sale_rate,$category_2_retail_rate);
                    $c += 3;
                }
                // barcode 12
                if(!empty($item[80]) && $item[80] != "NULL"){
                    $barcode_barcode = $item[80];
                    $uom_name = $item[81];
                    $packing = $item[82];
                    $purchase_rate = $item[83];
                    $category_1_sale_rate = $item[84]; // sale_price
                    $category_2_retail_rate = $item[85]; // retail_price
                    $barcode_id = $barcode_id+1;
                    $barcodeSR_id = $barcodeSR_id+2;
                    $this->insertBarcodeAndRates($product_id,$barcode_id,$barcodeSR_id,$barcode_barcode,$uom_name,$packing,$purchase_rate,$weight_apply,$category_1_sale_rate,$category_2_retail_rate);
                    $c += 3;
                }
            }
            $timeEnd = (microtime(true) - $startTime);
        }catch (Exception $e) {
            DB::rollback();
            $data = array_merge($data, Utilities::returnJsonImportForm());
            $data['message'] = 'Products Id: '.$error_product_id.' = Product Name: '.$error_product_name ;
            return $this->jsonErrorResponse($data, $e->getMessage(), 200);
           // return $this->jsonErrorResponse($data, $message, 200);
        }
        DB::commit();
        $data = array_merge($data, Utilities::returnJsonImportForm());
        $message = 'Products Successfully Imported <br/> Total Time Consume: '.number_format($timeEnd,3).' sec  <br/>';
        $data['loop'] = 'Loop Run '.$c;
        return $this->jsonSuccessResponse($data, $message, 200);

    }

    public function countProductByGroupId(Request $request){
        $data = [];
        if(isset($request->id)){
            $count = TblPurcProduct::where('group_item_id' , $request->id)->count();
            $data['count'] = $count + 1;
            return $this->jsonSuccessResponse($data , 'Success' , 200);
        }

        return $this->jsonErrorResponse($data , 'Something went wrong!' , 200);
    }

    public function insertBarcodeAndRates($product_id,$barcode_id,$barcodeSR_id,$barcode_barcode,$uom_name,$packing,$purchase_rate,$weight_apply,$category_1_sale_rate,$category_2_retail_rate){
        if(TblPurcProductBarcode::where('product_barcode_barcode','like',$barcode_barcode)->exists()){
            $barcode = TblPurcProductBarcode::where('product_barcode_barcode','like',$barcode_barcode)->first();
            unset($barcode_id);
            $barcode_id = $barcode->product_barcode_id;
        }else{
            TblPurcProductBarcode::create([
                'product_id' => $product_id,
                'product_barcode_id' => $barcode_id,
                'product_barcode_barcode' => $barcode_barcode,
                'uom_name' => (!empty($uom_name) && $uom_name != "NULL")?$uom_name:'',
                'product_barcode_packing' => (!empty($packing) && $packing != "NULL")?$packing:'',
                'product_barcode_purchase_rate' => (!empty($purchase_rate) && $purchase_rate != "NULL")?$purchase_rate:0,
                'product_barcode_weight_apply' => $weight_apply,
                'product_barcode_entry_status' => 1,
                'business_id' => 1,
                'product_barcode_user_id' => 91,
            ]);
        }
        $branch_id = 2;

        //	for Sale Rate

        TblPurcProductBarcodeSaleRate::create([
            'product_barcode_sale_rate_id' => $barcodeSR_id++,
            'product_barcode_id' => $barcode_id,
            'branch_id' => $branch_id,
            'product_category_id' => 1,
            'product_barcode_sale_rate_rate' => (!empty($category_1_sale_rate) && $category_1_sale_rate != "NULL")?$category_1_sale_rate:0,
            'product_barcode_barcode' => $barcode_barcode,
        ]);
        // for Retail Sale Rate
        TblPurcProductBarcodeSaleRate::create([
            'product_barcode_sale_rate_id' => $barcodeSR_id++,
            'product_barcode_id' => $barcode_id,
            'branch_id' => $branch_id,
            'product_category_id' => 2,
            'product_barcode_sale_rate_rate' => (!empty($category_2_retail_rate) && $category_2_retail_rate != "NULL")?$category_2_retail_rate:0,
            'product_barcode_barcode' => $barcode_barcode,
        ]);
        unset($product_id,$barcode_barcode,$uom_name,$packing,$purchase_rate,$weight_apply,$category_1_sale_rate,$category_2_retail_rate);
    }

    public function importExcleImageToBarcode(Request $request){
        $data = [];
        $validator = Validator::make($request->all(), [
            'file' => 'required|mimes:xlsx,xls,csv'
        ]);
        if ($validator->fails()) {
            $data['validator_errors'] = $validator->errors();
            return $this->jsonErrorResponse($data, trans('message.required_fields'), 422);
        }
        $error_product_id = '';
        $error_product_name = '';
        $startTime = microtime(true);
        $file = $request->file('file');
        $path = public_path('/upload/');
        $filename = $file->getClientOriginalName();
        $fileExtension = time() . '-' . $file->getClientOriginalExtension();
        $file->move($path,$filename);
        //  dd($path.$filename);

        DB::beginTransaction();
        try{
            $excel = Importer::make('Excel');
            $excel->load($path.$filename);
            $collection = $excel->getCollection();
            $c = 0;
            for ($row=1; $row < count($collection); $row++) {
                $item = $collection[$row];
                $img = [
                    'product_image_url' => $item[1]
                ];
                TblPurcProductBarcode::where('product_id',$item[0])->update($img);
            }
            $timeEnd = (microtime(true) - $startTime);
        }catch (Exception $e) {
            DB::rollback();
            $data = array_merge($data, Utilities::returnJsonImportForm());
            $data['message'] = 'Products Id: '.$error_product_id.' = Product Name: '.$error_product_name ;
            return $this->jsonErrorResponse($data, $e->getMessage(), 200);
            // return $this->jsonErrorResponse($data, $message, 200);
        }
        DB::commit();
        $data = array_merge($data, Utilities::returnJsonImportForm());
        $message = 'Products Successfully Imported <br/> Total Time Consume: '.number_format($timeEnd,3).' sec  <br/>';
        $data['loop'] = 'Loop Run '.$c;
        return $this->jsonSuccessResponse($data, $message, 200);
    }

    public function importExcleRate(Request $request){
        $data = [];
        $validator = Validator::make($request->all(), [
            'file' => 'required|mimes:xlsx,xls,csv'
        ]);
        if ($validator->fails()) {
            $data['validator_errors'] = $validator->errors();
            return $this->jsonErrorResponse($data, trans('message.required_fields'), 422);
        }
        $error_barcode_barcode = '';
        $startTime = microtime(true);
        $file = $request->file('file');
        $path = public_path('/upload/');
        $filename = $file->getClientOriginalName();
        $fileExtension = time() . '-' . $file->getClientOriginalExtension();
        $file->move($path,$filename);
        //  dd($path.$filename);

        DB::beginTransaction();
        try{
            $excel = Importer::make('Excel');
            $excel->load($path.$filename);
            $collection = $excel->getCollection();
            $c = 0;
            $barcodeSR_id = 4000000000;
            for ($row=1; $row < count($collection); $row++) {
                $item = $collection[$row];
                $error_barcode_barcode = $item[0];
                $barcode_barcode = $item[0];
                $rate = [
                    'product_barcode_sale_rate_rate' => $item[1]
                ];
                if(TblPurcProductBarcodeSaleRate::where('product_barcode_barcode',$barcode_barcode)
                    ->where('branch_id',1)->where('product_category_id',1)->exists()){
                    TblPurcProductBarcodeSaleRate::where('product_barcode_barcode',$barcode_barcode)
                        ->where('branch_id',1)
                        ->where('product_category_id',1)->update($rate);
                    TblPurcProductBarcodeSaleRate::where('product_barcode_barcode',$barcode_barcode)
                        ->where('branch_id',1)
                        ->where('product_category_id',2)->update($rate);
                    /*$barcode = TblPurcProductBarcodeSaleRate::where('product_barcode_barcode',$barcode_barcode)
                        ->where('branch_id',1)
                        ->where('product_category_id',2)->first();
                    $barcodeSR_id = $barcodeSR_id+1;
                    TblPurcProductBarcodeSaleRate::create([
                        'product_barcode_sale_rate_id' => $barcodeSR_id,
                        'product_barcode_id' => $barcode->product_barcode_id,
                        'branch_id' => 1,
                        'product_category_id' => 3,
                        'product_barcode_sale_rate_rate' => $item[2],
                        'product_barcode_barcode' => $barcode_barcode,
                    ]);*/
                    $c = $c+1;
                }
            }
            $timeEnd = (microtime(true) - $startTime);
        }catch (Exception $e) {
            DB::rollback();
            $data = array_merge($data, Utilities::returnJsonImportForm());
            $data['message'] = 'Barcode_Barcode: '.$error_barcode_barcode ;
            return $this->jsonErrorResponse($data, $e->getMessage(), 200);
            // return $this->jsonErrorResponse($data, $message, 200);
        }
        DB::commit();
        $data = array_merge($data, Utilities::returnJsonImportForm());
        $message = 'Products Successfully Imported <br/> Total Time Consume: '.number_format($timeEnd,3).' sec  <br/>';
        $data['loop'] = 'Loop Run '.$c;
        return $this->jsonSuccessResponse($data, $message, 200);
    }

    public function importExcleInsertSaleRate(Request $request){
        $data = [];
        $validator = Validator::make($request->all(), [
            'file' => 'required|mimes:xlsx,xls,csv'
        ]);
        if ($validator->fails()) {
            $data['validator_errors'] = $validator->errors();
            return $this->jsonErrorResponse($data, trans('message.required_fields'), 422);
        }
        $error_barcode_barcode = '';
        $startTime = microtime(true);
        $file = $request->file('file');
        $path = public_path('/upload/');
        $filename = $file->getClientOriginalName();
        $fileExtension = time() . '-' . $file->getClientOriginalExtension();
        $file->move($path,$filename);
        //  dd($path.$filename);

        DB::beginTransaction();
        try{
            $excel = Importer::make('Excel');
            $excel->load($path.$filename);
            $collection = $excel->getCollection();
            $c = 0;
            $uuid = (string)date("mdyis");
            $uuid .= 10001;
            $barcodeSR_id = (int)$uuid;
            for ($row=1; $row < count($collection); $row++) {
                $item = $collection[$row];
                $error_barcode_barcode = $item[0];
                $barcode_barcode = $item[0];
                $retail_rate = $item[1];
                $sale_rate = $item[2];
                if(TblPurcProductBarcode::where('product_barcode_barcode',$barcode_barcode)->exists()){
                    $barcode = TblPurcProductBarcode::where('product_barcode_barcode',$barcode_barcode)->first();
                    $barcode_id = $barcode->product_barcode_id;
                    //	for Sale Rate
                    TblPurcProductBarcodeSaleRate::create([
                        'product_barcode_sale_rate_id' => $barcodeSR_id++,
                        'product_barcode_id' => $barcode_id,
                        'branch_id' => 5,
                        'product_category_id' => 1,
                        'product_barcode_sale_rate_rate' => $sale_rate,
                        'product_barcode_barcode' => $barcode_barcode,
                    ]);
                    // for Retail Sale Rate
                    TblPurcProductBarcodeSaleRate::create([
                        'product_barcode_sale_rate_id' => $barcodeSR_id++,
                        'product_barcode_id' => $barcode_id,
                        'branch_id' => 5,
                        'product_category_id' => 2,
                        'product_barcode_sale_rate_rate' => $retail_rate,
                        'product_barcode_barcode' => $barcode_barcode,
                    ]);
                    //	for Whole Sale Rate
                    TblPurcProductBarcodeSaleRate::create([
                        'product_barcode_sale_rate_id' => $barcodeSR_id++,
                        'product_barcode_id' => $barcode_id,
                        'branch_id' => 5,
                        'product_category_id' => 3,
                        'product_barcode_sale_rate_rate' => 0,
                        'product_barcode_barcode' => $barcode_barcode,
                    ]);
                    $c = $c+1;
                    $barcodeSR_id = $barcodeSR_id+1;
                }
            }
            $timeEnd = (microtime(true) - $startTime);
        }catch (Exception $e) {
            DB::rollback();
            $data = array_merge($data, Utilities::returnJsonImportForm());
            $data['message'] = 'Barcode_Barcode: '.$error_barcode_barcode ;
            return $this->jsonErrorResponse($data, $e->getMessage(), 200);
            // return $this->jsonErrorResponse($data, $message, 200);
        }
        DB::commit();
        $data = array_merge($data, Utilities::returnJsonImportForm());
        $message = 'Products Successfully Imported <br/> Total Time Consume: '.number_format($timeEnd,3).' sec  <br/>';
        $data['loop'] = 'Loop Run '.$c;
        return $this->jsonSuccessResponse($data, $message, 200);
    }

    public function importExcleInsertPurcRate(Request $request){
        $data = [];
        $validator = Validator::make($request->all(), [
            'file' => 'required|mimes:xlsx,xls,csv'
        ]);
        if ($validator->fails()) {
            $data['validator_errors'] = $validator->errors();
            return $this->jsonErrorResponse($data, trans('message.required_fields'), 422);
        }
        $error_barcode_barcode = '';
        $startTime = microtime(true);
        $file = $request->file('file');
        $path = public_path('/upload/');
        $filename = $file->getClientOriginalName();
        $fileExtension = time() . '-' . $file->getClientOriginalExtension();
        $file->move($path,$filename);
        //  dd($path.$filename);

        DB::beginTransaction();
        try{
            $excel = Importer::make('Excel');
            $excel->load($path.$filename);
            $collection = $excel->getCollection();
            $c = 0;
            $uuid = (string)date("mdyis");
            $uuid .= 10001;
            $_id = (int)$uuid;
            for ($row=1; $row < count($collection); $row++) {
                $item = $collection[$row];
                $error_barcode_barcode = $item[0];
                $barcode_barcode = $item[0];
                $cost_rate = $item[1];
                if(TblPurcProductBarcode::where('product_barcode_barcode',$barcode_barcode)->exists()){
                    $barcode = TblPurcProductBarcode::where('product_barcode_barcode',$barcode_barcode)->first();
                    $product_id = $barcode->product_id;
                    $barcode_id = $barcode->product_barcode_id;
                    TblPurcProductBarcodePurchRate::create([
                        'product_barcode_purch_id' => $_id++,
                        'product_id' => $product_id,
                        'product_barcode_id' => $barcode_id,
                        'branch_id' => 5,
                        'product_barcode_purchase_rate' => $cost_rate,
                        'product_barcode_cost_rate' => $cost_rate,
                        'product_barcode_avg_rate' => 0,
                        'product_barcode_barcode' => $barcode_barcode,
                        'company_id' => 1,
                        'business_id' => 1,
                    ]);
                    $c = $c+1;
                    $_id = $_id+1;
                }
            }
            $timeEnd = (microtime(true) - $startTime);
        }catch (Exception $e) {
            DB::rollback();
            $data = array_merge($data, Utilities::returnJsonImportForm());
            $data['message'] = 'Barcode_Barcode: '.$error_barcode_barcode ;
            return $this->jsonErrorResponse($data, $e->getMessage(), 200);
            // return $this->jsonErrorResponse($data, $message, 200);
        }
        DB::commit();
        $data = array_merge($data, Utilities::returnJsonImportForm());
        $message = 'Products Successfully Imported <br/> Total Time Consume: '.number_format($timeEnd,3).' sec  <br/>';
        $data['loop'] = 'Loop Run '.$c;
        return $this->jsonSuccessResponse($data, $message, 200);
    }
}
