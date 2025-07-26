<?php

namespace App\Http\Controllers\Purchase;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Library\Utilities;
use App\Models\Defi\TblDefiFlavour;
use App\Models\Defi\TblDefiSeason;
use App\Models\Defi\TblDefiTaxGroup;
use App\Models\Defi\TblDefiVariant;
use App\Models\Defi\TblDefiWeight;
use App\Models\TblDefiColor;
use App\Models\TblDefiCountry;
use App\Models\TblDefiGSTCalculation;
use App\Models\TblDefiSize;
use App\Models\TblDefiTags;
use App\Models\TblDefiUom;
use App\Models\TblInveItemFormulationDtl;
use App\Models\TblInveStockDtl;
use App\Models\TblPurcBarcodeColor;
use App\Models\TblPurcBarcodeSize;
use App\Models\TblPurcBrand;
use App\Models\TblPurcDemandApprovalDtl;
use App\Models\TblPurcDemandDtl;
use App\Models\TblPurcGrnDtl;
use App\Models\TblPurcGroupItem;
use App\Models\TblPurcLpoDtl;
use App\Models\TblPurcManufacturer;
use App\Models\TblPurcPacking;
use App\Models\TblPurcProduct;
use App\Models\TblPurcProductBarcode;
use App\Models\TblPurcProductBarcodeDtl;
use App\Models\TblPurcProductBarcodePurchRate;
use App\Models\TblPurcProductBarcodeSaleRate;
use App\Models\TblPurcProductFOC;
use App\Models\TblPurcProductItemTag;
use App\Models\TblPurcProductLife;
use App\Models\TblPurcProductSpecificationTag;
use App\Models\TblPurcProductType;
use App\Models\TblPurcPurchaseOrderDtl;
use App\Models\TblPurcRateCategory;
use App\Models\TblPurcSupplier;
use App\Models\TblPurcWarrentyPeriod;
use App\Models\TblSaleSalesContractDtl;
use App\Models\TblSaleSalesDtl;
use App\Models\TblSaleSalesOrderDtl;
use App\Models\TblSoftBranch;
use App\Models\TblSoftProductTypeGroup;
use App\Models\TblSoftUserActivityLog;
use App\Models\User;
use App\Models\ViewInveDisplayLocation;
use App\Models\ViewPurcGroupItem;
use Illuminate\Http\Request;
use Image;
use Browser;
use Importer;

// db and Validator
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use Validator;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Support\Facades\Session;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ProductCardController extends Controller
{
    public static $page_title = 'Product';
    public static $redirect_url = 'product';
    public static $menu_dtl_id = '6';
    public static $autoBarcode = '';

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
        $data = [];
        $data['page_data']['title'] = self::$page_title;
        $data['page_data']['path_index'] = $this->prefixIndexPage.self::$redirect_url;;
        $data['page_data']['create'] = '/'.self::$redirect_url.$this->prefixCreatePage;
        if(isset($id)){
            if(!TblPurcProduct::where(Utilities::currentBC())->where('product_id',$id)->exists()){
                abort('404');
            }
            if($this->current_path == $this->page_view){
                $data['page_data'] = array_merge($data['page_data'], Utilities::viewForm());
                $data['permission'] = self::$menu_dtl_id.'-view';
            }
            if($this->current_path == $this->page_form){
                $data['page_data'] = array_merge($data['page_data'], Utilities::editForm());
                $data['permission'] = self::$menu_dtl_id.'-edit';
            }
            $data['id'] = $id;
            $data['current'] = TblPurcProduct::with('product_life','product_barcode','specification_tags','item_tags')->where(Utilities::currentBC())->where('product_id',$id)->first();
            $data['document_code'] = $data['current']->product_code;
            $data['product_foc'] = TblPurcProductFOC::with('supplier')->where('product_id','LIKE',$id)->orderBy('sr_no')->get();
            $data['page_data']['log_print'] = true;
        }else{
            $data['permission'] = self::$menu_dtl_id.'-create';
            $data['page_data'] = array_merge($data['page_data'], Utilities::newForm());
            $doc_data = [
                'biz_type'          => 'business',
                'model'             => 'TblPurcProduct',
                'code_field'        => 'product_code',
                'code_prefix'       => strtoupper('p')
            ];
            $data['document_code'] = Utilities::documentCode($doc_data);
        }
        $data['country'] = TblDefiCountry::where(Utilities::currentBC())->get();
        $data['warranty_period'] = TblPurcWarrentyPeriod::where('warrenty_period_entry_status',1)->where(Utilities::currentBC())->get();
        $data['group_item'] = ViewPurcGroupItem::where('group_item_level',3)->orderBy('group_item_name_string')
        ->where(Utilities::currentBC())
        ->get();
        // dd($data['group_item']);
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
        $data['suppliers'] = TblPurcSupplier::where('supplier_entry_status',1)->where(Utilities::currentBC())->orderBy('supplier_name')->get();
        $data['product_type'] = TblPurcProductType::where('product_type_entry_status',1)->where(Utilities::currentBC())->orderBy('product_type_name')->get();
        $data['flavour'] = TblDefiFlavour::where('flavour_entry_status',1)->where(Utilities::currentBC())->get();
        $data['season'] = TblDefiSeason::where('season_entry_status',1)->where(Utilities::currentBC())->get();
        $data['variant'] = TblDefiVariant::where('variant_entry_status',1)->where(Utilities::currentBC())->get();
        $data['weight'] = TblDefiWeight::where('weight_entry_status',1)->where(Utilities::currentBC())->get();
        $data['tax_group'] = TblDefiTaxGroup::where('tax_group_entry_status',1)->where(Utilities::currentBC())->get();
        $data['gst_clac'] = TblDefiGSTCalculation::where('gst_calculation_entry_status',1)->where(Utilities::currentBC())->get();
        $arr = [
            'biz_type' => 'business',
            'code' => $data['document_code'],
            'link' => $data['page_data']['create'],
            'table_name' => 'tbl_purc_product',
            'col_id' => 'product_id',
            'col_code' => 'product_code',
        ];
        $data['switch_entry'] = $this->switchEntry($arr);
        return view('purchase.product.form', compact('data'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request,$id = null)
    {
        $data = [];
        $validator = Validator::make($request->all(), [
            'product_name' => 'required|max:100',
            'product_control_group' => 'required|not_in:0',
            'product_item_type' => 'required|not_in:0',
        ],[
            'product_name.required' => 'Product Name is required',
            'product_name.max' => 'Product Name max length 100',
            'product_control_group.required' => 'Product group is required',
            'product_control_group.not_in' => 'Product group is required',
            'product_item_type.required' => 'Product type is required',
            'product_item_type.not_in' => 'Product type is required',
        ]);
        if ($validator->fails()) {
            $data['validator_errors'] = $validator->errors();
            $msg = trans('message.required_fields');
            foreach ($data['validator_errors']->messages() as $validator_errors) {
                $msg = $validator_errors[0];
                break;
            }
            return $this->jsonErrorResponse($data, $msg, 200);
        }

        $referer = Utilities::getReferer($request,$id);
        if($referer == $this->page_view){
            return $this->jsonErrorResponse($data, "Cannot update this entry.", 200);
        }
        DB::beginTransaction();
        try{
            $business_id = auth()->user()->business_id;
            $company_id = auth()->user()->company_id;
            $branch_id = auth()->user()->branch_id;
            $user_id = auth()->user()->id;

            if(isset($id)){
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

                $userFormLogsRemarks = "update product";
                $userFormLogsActionType = 'update';
                $product = TblPurcProduct::where('product_id',$id)->where(Utilities::currentBC())->first();
                $form_id = $product->product_id;
                $product->update_id = Utilities::uuid();

            }else{
                $product_sql = TblPurcProduct::where('product_name',$request->product_name)->where(Utilities::currentBC())->first();
                if(!empty($product_sql))
                {
                    if($product_sql->product_name === $request->product_name){
                        return $this->jsonErrorResponse($data, "Product Name is already exist.", 200);
                    }
                }

                $userFormLogsRemarks = "create product";
                $userFormLogsActionType = 'create';

                $form_id = Utilities::uuid();
                $doc_data = [
                    'biz_type'          => 'business',
                    'model'             => 'TblPurcProduct',
                    'code_field'        => 'product_code',
                    'code_prefix'       => strtoupper('p')
                ];
                $document_code = Utilities::documentCode($doc_data);
                $product = new TblPurcProduct();
                $product->product_id = $form_id;
                $product->product_code = $document_code;

            }
            $product->product_name = $request->product_name;
            $product->product_short_name = isset($request->product_short_name)?$request->product_short_name:'';
            $product->product_arabic_name = isset($request->product_arabic_name)?$request->product_arabic_name:'';
            $product->product_arabic_short_name = isset($request->product_arabic_short_name)?$request->product_arabic_short_name:'';
            $product->product_entry_status = isset($request->product_entry_status)?'1':'0';
            $product->product_can_sale = isset($request->product_can_sale)?'1':'0';
            $product->group_item_id = isset($request->product_control_group)?$request->product_control_group:'';
            $parent_group_item = TblPurcGroupItem::where('group_item_id',$request->product_control_group)->first();
            $product->group_item_parent_id = isset($parent_group_item->parent_group_id)?$parent_group_item->parent_group_id:'';
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
            $product->product_expiry_base = isset($request->product_expiry_base_on)?$request->product_expiry_base_on:'';
            $product->product_shelf_life_minimum = isset($request->product_shelf_life_minimum)?$request->product_shelf_life_minimum:'';
            $product->product_remarks = isset($request->product_remarks)?$request->product_remarks:'';
            $product->supplier_id = isset($request->supplier_id)?$request->supplier_id:'';
            $product->product_type_id = isset($request->product_type)?$request->product_type:'';
            $product->business_id = $business_id;
            $product->company_id = $company_id;
            $product->branch_id = $branch_id;
            $product->product_user_id = $user_id;
            $product->flavour_id = $request->flavour_id;
            $product->season_id = $request->season_id;
            $product->save();

            if(isset($request->product_item_tags)){
                TblPurcProductItemTag::where('product_id',$form_id)->where(Utilities::currentBC())->delete();
                foreach ($request->product_item_tags as $item_tags){
                    $tag = TblDefiTags::where('tags_id','LIKE',$item_tags)->where(Utilities::currentBC())->exists();
                    if(!$tag){
                        $arr = [
                            'name' => $item_tags,
                            'type' => config('constants.tags_type.Item'),
                            'branch_id' => $branch_id
                        ];
                        $item_tags = $this->CreateTag($arr);
                    }
                    TblPurcProductItemTag::create([
                        'item_tag_id'  => Utilities::uuid(),
                        'tag_id' => $item_tags,
                        'product_id' => $form_id,
                        'item_tag_entry_status' => 1,
                        'business_id' => $business_id,
                        'company_id' => $company_id,
                        'branch_id' => $branch_id,
                        'item_tag_user_id' => $user_id,
                    ]);
                }
            }
            /*
            if(isset($request->foc)){
                $focKey = 1;
                TblPurcProductFOC::where('product_id',$form_id)->delete();
                foreach ($request->foc as $focKey=>$foc){
                    if(!empty($foc['supplier_id']) && !empty($foc['supplier_branch_id'])) {
                        TblPurcProductFOC::create([
                            'product_foc_id' => Utilities::uuid(),
                            'sr_no' => $focKey,
                            'product_id' => $form_id,
                            'supplier_id' => $foc['supplier_id'],
                            'branch_id' => $foc['supplier_branch_id'],
                        ]);
                        $focKey = $focKey + 1;
                    }
                }
            }
            */

            if(isset($request->product_barcode_data)){
                if(count($request->product_barcode_data) == 0){
                    return $this->jsonErrorResponse($data, "Barcode is required" , 200);
                }

                $key_barcode_data = 1;
                $base_barcode = true;
                foreach ($request->product_barcode_data as $key=>$barcode_data){
                    /* check if barcode exists in table*/
                    $exits = TblPurcProductBarcode::where('product_barcode_barcode','like', $barcode_data['v_product_barcode'])->where('product_barcode_id', '!=' , $barcode_data['product_barcode_id'])->get();
                    if (count($exits) > 0) {
                        $uom = TblDefiUom::where('uom_id',$barcode_data['uom_packing_uom'])->first();
                        $messg = "Barcode: ".$barcode_data['v_product_barcode']."</br>";
                        $messg .= "UOM: ".$uom->uom_name."</br>";
                        $messg .= "Packing: ".$barcode_data['product_barcode_packing']."</br>";
                        $messg .= " already exists.";
                        return $this->jsonErrorResponse($data, $messg , 200);
                    }
                    /*end exists barcode check*/

                    if(isset($id) && isset($barcode_data['product_barcode_id']) && TblPurcProductBarcode::where('product_barcode_id',$barcode_data['product_barcode_id'])->exists()){
                        $product_barcode_id = $barcode_data['product_barcode_id'];
                        $barcode = TblPurcProductBarcode::where('product_barcode_id',$product_barcode_id)->first();
                    }else{
                        $product_barcode_id = Utilities::uuid();
                        $barcode = new TblPurcProductBarcode();
                        $barcode->product_barcode_id = $product_barcode_id;
                    }
                    $v_product_barcode = trim($barcode_data['v_product_barcode']);
                    $barcode->product_id = $form_id;
                    $barcode->product_barcode_barcode = $v_product_barcode;
                    $barcode->product_barcode_entry_status = 1;
                    $barcode->product_barcode_sr_no = $key_barcode_data;
                    $barcode->product_barcode_purchase_rate = isset($barcode_data['barcode_rate_purchase_rate'])?$barcode_data['barcode_rate_purchase_rate']:'';
                    $barcode->uom_id = $barcode_data["uom_packing_uom"];
                    $barcode->product_barcode_packing = $barcode_data["product_barcode_packing"];
                    $barcode->variant_id = $barcode_data["uom_packing_other_tag"];
                    $barcode->color_id = $barcode_data["uom_packing_color_tag"];
                    $barcode->size_id = $barcode_data["uom_packing_size_tag"];
                    $barcode->weight_id = $barcode_data["weight_id"];
                    $barcode->product_barcode_user_id = auth()->user()->id;
                    $barcode->business_id = auth()->user()->business_id;
                    $barcode->product_barcode_weight_apply = isset($barcode_data["product_barcode_weight_apply"])?1:"";

                    if($base_barcode && isset($barcode_data["base_barcode"])){
                        $barcode->base_barcode = 1;
                        $base_barcode = false;
                    }else{
                        $barcode->base_barcode = 0;
                    }

                    if(isset($barcode_data['product_image'])) {
                        //  $image = $barcode_data->file('product_image');
                        $filename = time().$key_barcode_data. '.' . $barcode_data['product_image']->getClientOriginalExtension();
                        $path = public_path('/products/' . $filename);
                        Image::make($barcode_data['product_image']->getRealPath())->resize(200, 200)->save($path);
                        $barcode->product_image_url = isset($filename)?$filename:'';
                    }
                    $barcode->save();

                    $branches = TblSoftBranch::where('branch_active_status',1)->where(Utilities::currentBC())->get();
                    $i = 0;
                    foreach($branches as $branch){
                        $purcRate = TblPurcProductBarcodePurchRate::where('branch_id',$branch->branch_id)
                            ->where(['product_id'=>$form_id,'product_barcode_id'=>$product_barcode_id])->first();
                        if(!empty($purcRate)){
                            $old_sale_rate = $purcRate->sale_rate;
                            $old_net_tp = $purcRate->net_tp;
                            $old_updated_at = $purcRate->updated_at;
                            //$purcRate->product_barcode_cost_rate = $this->addNo($barcode_data["cost_rate_$i"]);
                            $purcRate->net_tp = $this->addNo($barcode_data["cost_rate_$i"]);
                            $purcRate->sale_rate = $this->addNo($barcode_data["sale_rate_$i"]);
                            $purcRate->whole_sale_rate = $this->addNo($barcode_data["whole_sale_rate_$i"]);
                            $purcRate->tax_rate = $this->addNo($barcode_data["tax_value_$i"]);
                            $purcRate->inclusive_tax_price = $this->addNo($barcode_data["inclusive_tax_price_$i"]);
                            $purcRate->gp_perc = $this->addNo($barcode_data["gp_perc_$i"]);
                            $purcRate->gp_amount = $this->addNo($barcode_data["gp_amount_$i"]);
                            $purcRate->business_id = $business_id;
                            $purcRate->company_id = $company_id;
                            $purcRate->branch_id = $barcode_data["rate_branchId_$i"];
                            $purcRate->hs_code = $barcode_data["hs_code_$i"];
                            $purcRate->tax_group_id = $barcode_data["tax_group_id_$i"];
                            $purcRate->gst_calculation_id = $barcode_data["gst_calculation_id_$i"];
                            $purcRate->save();
                        }else{
                            $purcRate = TblPurcProductBarcodePurchRate::create([
                                'product_barcode_purch_id' => Utilities::uuid(),
                                'product_id' => $form_id,
                                'product_barcode_id' => $product_barcode_id,
                                'product_barcode_barcode' => $v_product_barcode,
                                //'product_barcode_cost_rate' => $this->addNo($barcode_data["cost_rate_$i"]),
                                'net_tp' => $this->addNo($barcode_data["cost_rate_$i"]),
                                'sale_rate' => $this->addNo($barcode_data["sale_rate_$i"]),
                                'whole_sale_rate' => $this->addNo($barcode_data["whole_sale_rate_$i"]),
                                'tax_rate' => $this->addNo($barcode_data["tax_value_$i"]),
                                'inclusive_tax_price' => $this->addNo($barcode_data["inclusive_tax_price_$i"]),
                                'gp_perc' => $this->addNo($barcode_data["gp_perc_$i"]),
                                'gp_amount' => $this->addNo($barcode_data["gp_amount_$i"]),
                                'business_id' => $business_id,
                                'company_id' => $company_id,
                                'branch_id' => $barcode_data["rate_branchId_$i"],
                                'hs_code' => $barcode_data["hs_code_$i"],
                                'tax_group_id' => $barcode_data["tax_group_id_$i"],
                                'gst_calculation_id' => $barcode_data["gst_calculation_id_$i"],
                            ]);

                            $old_sale_rate = "";
                            $old_net_tp = "";
                            $old_updated_at = "";
                        }

                        /* start=> add product log */
                        $req = [
                            "document_id" => $form_id,
                            "product_barcode_purch_id" => $purcRate->product_barcode_purch_id,
                            "product_id" => $form_id,
                            "product_barcode_id" => $purcRate->product_barcode_id,
                            "product_barcode_barcode" => $purcRate->product_barcode_barcode,
                            //"product_barcode_cost_rate" => $this->addNo($barcode_data["cost_rate_$i"]),
                            "net_tp" => $this->addNo($barcode_data["cost_rate_$i"]),
                            "sale_rate" => $this->addNo($barcode_data["sale_rate_$i"]),
                            "whole_sale_rate" => $this->addNo($barcode_data["whole_sale_rate_$i"]),
                            'tax_rate' => $this->addNo($barcode_data["tax_value_$i"]),
                            'inclusive_tax_price' => $this->addNo($barcode_data["inclusive_tax_price_$i"]),
                            "gp_perc" => $this->addNo($barcode_data["gp_perc_$i"]),
                            "gp_amount" => $this->addNo($barcode_data["gp_amount_$i"]),
                            'hs_code' => $barcode_data["hs_code_$i"],
                            'tax_group_id' => $barcode_data["tax_group_id_$i"],
                            'gst_calculation_id' => $barcode_data["gst_calculation_id_$i"],
                            "business_id" => $business_id,
                            "company_id" => $company_id,
                            "branch_id" => $barcode_data["rate_branchId_$i"],
                            "user_id" => auth()->user()->id,
                            "old_sale_rate" => $old_sale_rate,
                            "old_net_tp" => $old_net_tp,
                            "old_created_date" => date('Y-m-d H:i:s', strtotime($old_updated_at)),
                            "activity_form_type" => "product",
                            "activity_form_action" => isset($id)?"create":"update",
                        ];
                        $return = $this->storeRateLog($req);
                        if(!isset($return->original['status']) && $return->original['status'] != 'success'){
                            return $this->jsonErrorResponse($data, "Rate log not update...", 200);
                        }
                        /* end=> add product log */

                        $i = $i + 1;
                    }

                    $branchCount = TblSoftBranch::where('branch_active_status',1)->where(Utilities::currentBC())->count();

                    TblPurcProductBarcodeDtl::where('product_barcode_id',$product_barcode_id)->delete();
                    for ($i=0;$branchCount>$i; $i++ ){
                        if($barcode_data["branch_id_$i"] == $barcode_data["stock_branch_id_$i"] && $barcode_data["stock_branch_id_$i"] == $barcode_data["rate_branchId_$i"]){
                            $BranchDtl_branch_id = $barcode_data["branch_id_$i"];
                        }else{
                            $data['branch_error'] = 'Branch Error';
                            $data['stock_branch_id'] = $barcode_data["stock_branch_id_$i"];
                            return $this->jsonErrorResponse($data, trans('message.error'), 200);
                        }
                        TblPurcProductBarcodeDtl::create([
                            'product_barcode_dtl_id' => Utilities::uuid(),
                            'product_barcode_id' => $product_barcode_id,
                            'product_barcode_stock_limit_neg_stock' => isset($barcode_data["stock_limit_neg_stock_$i"][0])?'1':'0',
                            'product_barcode_stock_limit_reorder_point' => $barcode_data["stock_limit_reorder_point_$i"],
                            'product_barcode_stock_limit_reorder_qty' => $barcode_data["stock_qty_level_$i"],
                            'product_barcode_shelf_stock_max_qty' => $barcode_data["stock_max_limit_$i"],
                            'product_barcode_shelf_stock_min_qty' => $barcode_data["stock_min_limit_$i"],
                            'product_barcode_stock_cons_day' => $barcode_data["stock_consumption_days_$i"],
                            'product_barcode_stock_limit_limit_apply' => isset($barcode_data["stock_limit_apply_status_$i"][0])?'1':'0',
                            'product_barcode_stock_limit_status' => isset($barcode_data["stock_status_$i"][0])?'1':'0',

                            'product_barcode_shelf_stock_location' => !empty($barcode_data["shelf_stock_location_$i"])?$barcode_data["shelf_stock_location_$i"]:"",
                            'product_barcode_shelf_stock_sales_man' => isset($barcode_data["shelf_stock_salesman_$i"])?$barcode_data["shelf_stock_salesman_$i"]:' ',
                            'product_barcode_stock_limit_max_qty' => $barcode_data["shelf_stock_max_qty_$i"],
                            'product_barcode_stock_limit_min_qty' => $barcode_data["shelf_stock_min_qty_$i"],
                            'product_barcode_tax_value' => isset($barcode_data["tax_tax_value_$i"])?$barcode_data["tax_tax_value_$i"]:0,
                            'product_barcode_tax_apply' => isset($barcode_data["tax_tax_status_$i"][0])?'1':'0',
                            'product_barcode_shelf_stock_reorder_point' => $barcode_data["shelf_stock_reorder_point_$i"],
                            'product_barcode_shelf_stock_dept_qty' => $barcode_data["shelf_stock_dept_qty_$i"],
                            'product_barcode_shelf_stock_face_qty' => $barcode_data["shelf_stock_face_qty_$i"],
                            'business_id' => $business_id,
                            'company_id' => $company_id,
                            'branch_id' => $BranchDtl_branch_id,
                        ]);
                    }

                    $key_barcode_data = $key_barcode_data + 1;
                } // end loop barcode insert and update
            }
            $form_data = TblPurcProduct::with('product_life','specification_tags','item_tags','product_foc','product_barcode')->where('product_id',$form_id)->where(Utilities::currentBC())->first()->toArray();
            $log = [
                'menu_dtl_id' => self::$menu_dtl_id,
                'document_id' => $form_id,
                'document_name' => 'product',
                'activity_form_menu_dtl_id' => self::$menu_dtl_id,
                'activity_form_id' => $form_id,
                'activity_form_type' => 'product',
                'action_type' => $userFormLogsActionType,
                'form_data' => serialize((object)$form_data),
                'remarks' => $userFormLogsRemarks,
            ];
            $this->userFormLogs($log);

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
            // $data['redirect'] = $this->prefixIndexPage.self::$redirect_url;
            $data['redirect'] = '/'.self::$redirect_url.$this->prefixCreatePage.'/'.$form_id;
            return $this->jsonSuccessResponse($data, trans('message.update'), 200);
        }else{
            $data = array_merge($data, Utilities::returnJsonNewForm());
            $data['redirect'] = '/'.self::$redirect_url.$this->prefixCreatePage;
            return $this->jsonSuccessResponse($data, trans('message.create'), 200);
        }
    }

    public function storeRateLog($req){
        $request = (object)$req;

        $log_product_barcode_purch_id = Utilities::uuid();
        $document_id = (isset($request->document_id))?$request->document_id:"";
        $product_barcode_purch_id = (isset($request->product_barcode_purch_id))?$request->product_barcode_purch_id:"";
        $product_id = (isset($request->product_id))?$request->product_id:"";
        $product_barcode_id = (isset($request->product_barcode_id))?$request->product_barcode_id:"";
        $product_barcode_barcode = (isset($request->product_barcode_barcode))?$request->product_barcode_barcode:"";
        $branch_id = (isset($request->branch_id))?$request->branch_id:"";
        $product_barcode_purchase_rate = (isset($request->product_barcode_purchase_rate))?$request->product_barcode_purchase_rate:"";
        //$product_barcode_cost_rate = (isset($request->product_barcode_cost_rate))?$request->product_barcode_cost_rate:"";
        $product_barcode_cost_rate = (isset($request->net_tp))?$request->net_tp:"";
        $product_barcode_avg_rate = (isset($request->product_barcode_avg_rate))?$request->product_barcode_avg_rate:"";
        $company_id = (isset($request->company_id))?$request->company_id:"";
        $business_id = (isset($request->business_id))?$request->business_id:"";
        $sale_rate = (isset($request->sale_rate))?$request->sale_rate:"";
        $tax_rate = (isset($request->tax_rate))?$request->tax_rate:"";
        $inclusive_tax_price = (isset($request->inclusive_tax_price))?$request->inclusive_tax_price:"";
        $gp_perc = (isset($request->gp_perc))?$request->gp_perc:"";
        $gp_amount = (isset($request->gp_amount))?$request->gp_amount:"";
        $hs_code = (isset($request->hs_code))?$request->hs_code:"";
        $tax_group_id = (isset($request->tax_group_id))?$request->tax_group_id:"";
        $gst_calculation_id = (isset($request->gst_calculation_id))?$request->gst_calculation_id:"";
        $whole_sale_rate = (isset($request->whole_sale_rate))?$request->whole_sale_rate:"";
        $mrp = (isset($request->mrp))?$request->mrp:"";
        $product_barcode_minimum_profit = (isset($request->product_barcode_minimum_profit))?$request->product_barcode_minimum_profit:"";
        $last_tp = (isset($request->last_tp))?$request->last_tp:"";
        $supplier_last_tp = (isset($request->supplier_last_tp))?$request->supplier_last_tp:"";
        $last_gst_perc = (isset($request->last_gst_perc))?$request->last_gst_perc:"";
        $net_tp = (isset($request->net_tp))?$request->net_tp:"";
        $activity_form_type = (isset($request->activity_form_type))?$request->activity_form_type:"";
        $activity_form_action = (isset($request->activity_form_action))?$request->activity_form_action:"";
        $user_id = (isset($request->user_id))?$request->user_id:"";
        $old_sale_rate = (isset($request->old_sale_rate))?$request->old_sale_rate:"";
        $old_net_tp = (isset($request->old_net_tp))?$request->old_net_tp:"";
        $old_created_date = (isset($request->old_created_date))?$request->old_created_date:"";
        $data = [];
        DB::beginTransaction();
        try{

            $rate = \App\Models\Log\TblLogPurcProductBarcodePurchRate::create([
                'document_id' => $document_id,
                'log_product_barcode_purch_id' => $log_product_barcode_purch_id,
                'product_barcode_purch_id' => $product_barcode_purch_id,
                'product_id' => $product_id,
                'product_barcode_id' => $product_barcode_id,
                'product_barcode_barcode' => $product_barcode_barcode,
                'branch_id' => $branch_id,
                'product_barcode_purchase_rate' => $product_barcode_purchase_rate,
                //'product_barcode_cost_rate' => $product_barcode_cost_rate,
                'net_tp' => $product_barcode_cost_rate,
                'product_barcode_avg_rate' => $product_barcode_avg_rate,
                'company_id' => $company_id,
                'business_id' => $business_id,
                'sale_rate' => $sale_rate,
                'tax_rate' => $tax_rate,
                'inclusive_tax_price' => $inclusive_tax_price,
                'gp_perc' => $gp_perc,
                'gp_amount' => $gp_amount,
                'hs_code' => $hs_code,
                'tax_group_id' => $tax_group_id,
                'gst_calculation_id' => $gst_calculation_id,
                'whole_sale_rate' => $whole_sale_rate,
                'mrp' => $mrp,
                'product_barcode_minimum_profit' => $product_barcode_minimum_profit,
                'last_tp' => $last_tp,
                'supplier_last_tp' => $supplier_last_tp,
                'last_gst_perc' => $last_gst_perc,
                'net_tp' => $net_tp,
                'browser_dtl' => serialize($this->get_user_browser()),
                'user_ip' => $_SERVER['REMOTE_ADDR'],
                'activity_form_type' => $activity_form_type,
                'activity_form_action' => $activity_form_action,
                'user_id' => $user_id,
                'old_sale_rate' => $old_sale_rate,
                'old_net_tp' => $old_net_tp,
                'old_created_date' => $old_created_date,
            ]);

        }catch (Exception $e) {
            DB::rollback();
            $data = [ 'message'=> $e->getMessage(), 'status'=>'error' ];
            return response()->json($data,200);
        }
        DB::commit();
        $data = [ 'message'=>trans('message.create'), 'status'=>'success' ];
        return response()->json($data,200);
    }

    public function CreateTag($arr){
        $uuid = Utilities::uuid();
        TblDefiTags::create([
            'tags_id'  => $uuid,
            'tags_name' => $arr['name'],
            'tags_type' => $arr['type'],
            'tags_entry_status' => 1,
            'business_id' => auth()->user()->business_id,
            'company_id' => auth()->user()->company_id,
            'branch_id' => $arr['branch_id'],
            'tags_user_id' => auth()->user()->id,
        ]);
        return $uuid;
    }
    public function CreateColorTag($arr){
        $uuid = Utilities::uuid();
        TblDefiColor::create([
            'color_id'  => $uuid,
            'color_name' => $arr['name'],
            'color_entry_status' => 1,
            'business_id' => auth()->user()->business_id,
            'company_id' => auth()->user()->company_id,
            'branch_id' => $arr['branch_id'],
            'color_user_id' => auth()->user()->id,
        ]);
        return $uuid;
    }
    public function CreateSizeTag($arr){
        $uuid = Utilities::uuid();
        TblDefiSize::create([
            'size_id'  => $uuid,
            'size_name' => $arr['name'],
            'size_entry_status' => 1,
            'business_id' => auth()->user()->business_id,
            'company_id' => auth()->user()->company_id,
            'branch_id' => $arr['branch_id'],
            'size_user_id' => auth()->user()->id,
        ]);
        return $uuid;
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
                    $barcode->purc_rate()->delete();
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

    public function destroyBarcode($id)
    {
        $data = [];
        DB::beginTransaction();
        try{
            $po = TblPurcPurchaseOrderDtl::where('product_barcode_id',$id)->first();
            $grn = TblPurcGrnDtl::where('product_barcode_id',$id)->first();
            $si = TblSaleSalesDtl::where('product_barcode_id',$id)->first();
            $stock = TblInveStockDtl::where('product_barcode_id',$id)->first();

            if(empty($po) && empty($grn)
                && empty($si) && empty($stock) ) {

                TblPurcProductBarcodeDtl::where('product_barcode_id',$id)->delete();
                TblPurcProductBarcodePurchRate::where('product_barcode_id',$id)->delete();
                TblPurcProductBarcode::where('product_barcode_id',$id)->delete();

            }else{
                return $this->jsonErrorResponse($data, trans('message.not_delete'), 200);
            }

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

    public function autoBarcodeGenerate(){
        $data = [];
        $data['barcode'] = "";
        $new_barcode = Utilities::uuid();
        $barcode = $this->autoBarcodeGenerateChecking($new_barcode);
        if($barcode['status'] == 'success'){
            self::$autoBarcode = $new_barcode;
            $data['barcode'] = self::$autoBarcode;
        }
        return $this->jsonSuccessResponse($data,'', 200);
    }

    public function autoBarcodeGenerateChecking($barcode){
        $bb = TblPurcProductBarcode::where('product_barcode_barcode',$barcode)->exists();
        if(!empty($bb)){
            $barcode = Utilities::uuid();
            $this->autoBarcodeGenerateChecking($barcode);
        }
        $data['status'] = 'success';
        return $data;
    }
}
