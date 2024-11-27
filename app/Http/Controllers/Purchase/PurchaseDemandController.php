<?php

namespace App\Http\Controllers\Purchase;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\TblPurcDemand;
use App\Models\TblPurcDemandDtl;
use App\Models\TblPurcProduct;
use App\Models\TblPurcProductBarcode;
use App\Models\TblPurcProductBarcodeSaleRate;
use App\Models\TblDefiUom;
use App\Models\TblPurcPacking;
use App\Models\TblPurcSupplier;
use App\Models\TblPurcDemandApprovalDtl;
use App\Models\TblSoftUserPageSetting;
use App\Models\User;
use App\Models\ViewInveDisplayLocation;
use Illuminate\Http\Request;
// db and Validator
use App\Library\Utilities;
use Illuminate\Validation\Rule;
use Validator;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;


class PurchaseDemandController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public static $page_title = 'Purchase Demand';
    public static $redirect_url = 'demand';
    public static $menu_dtl_id = '9';
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
        $type = 'purchase_demand';
        $data['form_type'] = 'purc_demand';
        $data['page_data'] = [];
        $data['page_data']['title'] = self::$page_title;
        $data['page_data']['path_index'] = $this->prefixIndexPage.self::$redirect_url;
        $data['page_data']['create'] = '/'.self::$redirect_url.$this->prefixCreatePage;
        $data['page_data']['pending_pr'] = TRUE;
        if(isset($id)){
            if(TblPurcDemand::where('demand_id','LIKE',$id)->where(Utilities::currentBCB())->exists()){
                $data['page_data'] = array_merge($data['page_data'], Utilities::editForm());
                $data['permission'] = self::$menu_dtl_id.'-edit';
                $data['id'] = $id;
                $data['current'] = TblPurcDemand::with('dtls','supplier','salesman')->where('demand_id',$id)->where('demand_type',$type)->where(Utilities::currentBCB())->first();
                $data['document_code'] = $data['current']->demand_no;
                $data['page_data']['print'] = '/'.self::$redirect_url.'/print/'.$id;
            }else{
                abort('404');
            }
        }else{
            $data['permission'] = self::$menu_dtl_id.'-create';
            $data['page_data'] = array_merge($data['page_data'], Utilities::newForm());
            $doc_data = [
                'biz_type'          => 'branch',
                'model'             => 'TblPurcDemand',
                'code_field'        => 'demand_no',
                'code_prefix'       => strtoupper('d'),
                'code_type_field'   => 'demand_type',
                'code_type'         => $type,
            ];
            $data['document_code'] = Utilities::documentCode($doc_data);
        }
        $data['users'] = User::where('user_type','erp')->where('user_entry_status',1)->where(Utilities::currentBC())->get();
        $arr = [
            'biz_type' => 'branch',
            'code' => $data['document_code'],
            'link' => $data['page_data']['create'],
            'table_name' => 'tbl_purc_demand',
            'col_id' => 'demand_id',
            'col_code' => 'demand_no',
            'code_type_field'   => 'demand_type',
            'code_type'         => $type,
        ];
        $data['switch_entry'] = $this->switchEntry($arr);
        return view('purchase.purchase_demand.form',compact('data'));
    }

    public function ajaxGetDemandItems($id = null){

    }
    public function items()
    {
        $data = DB::table('tbl_purc_product')
            ->join('tbl_purc_product_barcode', 'tbl_purc_product.product_id', '=', 'tbl_purc_product_barcode.product_id')
            ->join('tbl_purc_product_barcode_uom_packing', 'tbl_purc_product_barcode.product_barcode_id', '=', 'tbl_purc_product_barcode_uom_packing.product_barcode_uom_packing_id')
            ->join('tbl_defi_uom', 'tbl_defi_uom.uom_id', '=', 'tbl_purc_product_barcode_uom_packing.uom_id')
            ->join('tbl_purc_packing', 'tbl_purc_packing.packing_id', '=', 'tbl_purc_product_barcode_uom_packing.packing_id')
            ->where('tbl_purc_product.business_id',auth()->user()->business_id)
            ->where('tbl_purc_product.company_id',auth()->user()->company_id)
            ->select("tbl_purc_product.*",'tbl_defi_uom.uom_name','tbl_purc_packing.packing_name','tbl_purc_product.product_id', 'tbl_purc_product.product_name', 'tbl_purc_product_barcode.product_barcode_barcode','tbl_purc_product_barcode_uom_packing.uom_id','tbl_purc_product_barcode_uom_packing.packing_id')
            ->get();
        return view('purchase.purchase_demand.items',compact('data'));
    }


    public function BarcodeDtl($id,$type = null,$store =null)
    {
       // dd($type);
        if(TblPurcProductBarcode::where('product_barcode_barcode','LIKE',$id)->exists()){
            $code = $id;
        }else{
            $weight_prod = substr($id, 0, 7);
            if(TblPurcProductBarcode::where('product_barcode_barcode','LIKE',$weight_prod)->where('product_barcode_weight_apply',1)->exists()){
                $code = $weight_prod;
            }
        }
        if(isset($code) && !empty($code)){
            $data['data'] = TblPurcProductBarcode::with('product','barcode_dtl','uom','packing')->where('product_barcode_barcode',$code)->first();
            $data['rate'] = TblPurcProductBarcodeSaleRate::where('product_barcode_id',$data['data']['product_barcode_id'])->where('branch_id',auth()->user()->branch_id)->where('product_category_id',2)->first();
            $now = new \DateTime("now");
            $today_format = $now->format("d-m-Y");
            $date = date('Y-m-d', strtotime($today_format));

            if($type == 'purc_demand'){
                $arr = [
                    $data['data']['product_id'],
                    $data['data']['product_barcode_id'],
                    auth()->user()->business_id,
                    auth()->user()->company_id,
                    auth()->user()->branch_id,
                    '',
                    $date
                ];

                $store_stock = 0;
              //  dd("8888196160423");
                $store_stock =  collect(DB::select('SELECT get_stock_current_qty_date(?,?,?,?,?,?,?) AS code from dual', $arr))->first()->code;
            }
            if($type == 'sa'){
                $data['product_exists'] =   false;
                $now = new \DateTime("now");
                $today_format = $now->format("d-m-Y");
                $date = date('Y-m-d', strtotime($today_format));
                $arr = [
                    $data['data']['product_id'],
                    $data['data']['product_barcode_id'],
                    auth()->user()->business_id,
                    auth()->user()->company_id,
                    auth()->user()->branch_id,
                    $store,
                    $date
                ];
                /* start #1
                 * if same Product (not barcode) scanned twice in another document no then system should inform
                 * that "this product already entered in document no 'SA-?????'"
                 * */
                $q = "select s.stock_code from TBL_INVE_STOCK s
                    join TBL_INVE_STOCK_DTL sd on s.stock_id = sd.STOCK_ID
                    where sd.product_id = '".$data['data']['product_id']."' and lower(s.stock_code_type) = 'sa'
                    and s.stock_date > to_date('01-12-2021', 'dd/mm/yyyy')
                    and s.branch_id = ".auth()->user()->branch_id." order by stock_code desc";

                $getStockCode = DB::selectOne($q);
                if(isset($getStockCode->stock_code)){
                    $data['product_exists'] =   true;
                    $data['product_exists_msg'] =   "This product already exists in document No. $getStockCode->stock_code";
                };
                $store_stock = 0;
                if(!isset($getStockCode->stock_code)){
                    $store_stock_qty =  collect(DB::select('SELECT GET_STOCK_CURRENT_QTY_DATE_OPENING(?,?,?,?,?,?,?) AS qty from dual', $arr))->first()->qty;
                    $packing    = isset($data['data']['product_barcode_packing']) && !empty($data['data']['product_barcode_packing'])?$data['data']['product_barcode_packing']:1;

                    $store_stock = $store_stock_qty / (float)$packing;
                }

                /* start #2
                 * in opening stock, when add barcode (when login other than main branch)
                 * system should check that barcode already added in opening stock (in main branch) or no.
                 * if not exist then should show only message
                 * "this barcode not found in opening stock of main branch"
                 * */
                $q = "select s.stock_code from TBL_INVE_STOCK s
                    join TBL_INVE_STOCK_DTL sd on s.stock_id = sd.STOCK_ID
                    where sd.product_id = '".$data['data']['product_id']."' and lower(s.stock_code_type) = 'sa'
                    and s.branch_id = ".Helper::$DefaultBranch." order by stock_code desc";
                $getStock = DB::selectOne($q);

                if(empty($getStock) && !isset($getStockCode->stock_code)){
                    if(Helper::$DefaultBranch == auth()->user()->branch_id){
                        $data['product_exists'] =   false;
                    }else{
                        $data['product_exists'] =   true;
                    }
                    $data['product_exists_msg'] =   "This product not found in main branch";
                };
                /* End #2
                 * */
            }
            $collection = collect($data['data']);
            $data['data'] = $collection->put('store_stock' , isset($store_stock) ? $store_stock : 0);
            $data['data'] = $collection->put('branch_id' , auth()->user()->branch_id);
            $data['display_location'] = ViewInveDisplayLocation::orderBy('display_location_name_string')->where('branch_id',auth()->user()->branch_id)->get();
            $data['uomData'] = TblPurcProductBarcode::with('uom')->where('product_id',$data['data']['product_id'])->get();

        }else{
            $data['data'] = null;
        }

        return response()->json($data);
    }

    public function ProdUOM($id)
    {
        $data['data'] = TblPurcProductBarcode::with('uom')->where('product_id',$id)->get();
        return response()->json($data);
    }

    public function ProdRate($id)
    {
        $rate = $data['rate'] = TblPurcProductBarcodeSaleRate::where('product_barcode_id',$id)->where('branch_id',auth()->user()->branch_id)->where('product_category_id',1)->first();
        return response()->json($rate);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $id = null)
    {
        $type = 'purchase_demand';
        $data = [];
        $validator = Validator::make($request->all(), [
            'supplier_id' => 'nullable|numeric',
            'salesman' => 'nullable|numeric',
        ]);
        if ($validator->fails()) {
            $data['validator_errors'] = $validator->errors();
            return $this->jsonErrorResponse($data, trans('message.required_fields'), 422);
        }
        DB::beginTransaction();
        try{
            if(isset($id)){
                $purchaseDemand = TblPurcDemand::where('demand_id',$id)->where('demand_type',$type)->where(Utilities::currentBCB())->first();
            }else{
                $purchaseDemand = new TblPurcDemand();
                $purchaseDemand->demand_id = Utilities::uuid();
                $doc_data = [
                    'biz_type'          => 'branch',
                    'model'             => 'TblPurcDemand',
                    'code_field'        => 'demand_no',
                    'code_prefix'       => strtoupper('d'),
                    'code_type_field'   => 'demand_type',
                    'code_type'         => $type,
                ];
                $purchaseDemand->demand_no = Utilities::documentCode($doc_data);
            }
            $form_id = $purchaseDemand->demand_id;
            $purchaseDemand->demand_type = $type;
            $purchaseDemand->supplier_id = $request->supplier_id;
            $purchaseDemand->demand_date = date('Y-m-d', strtotime($request->demand_date));
            $purchaseDemand->demand_forward_for_approval = 1;
            $purchaseDemand->salesman_id = $request->salesman;
            $purchaseDemand->demand_notes = $request->demand_notes;
            $purchaseDemand->demand_entry_status = 1;
            $purchaseDemand->business_id = auth()->user()->business_id;
            $purchaseDemand->company_id = auth()->user()->company_id;
            $purchaseDemand->branch_id = auth()->user()->branch_id;
            $purchaseDemand->demand_user_id = auth()->user()->id;
            $purchaseDemand->save();

            $del_DemandDtls = TblPurcDemandDtl::where('demand_id',$id)->where(Utilities::currentBCB())->get();
            foreach ($del_DemandDtls as $del_DemandDtl){
                TblPurcDemandDtl::where('demand_dtl_id',$del_DemandDtl->demand_dtl_id)->where(Utilities::currentBCB())->delete();
            }

            if(isset($request->pd)){
                $sr_no = 1;
                foreach ($request->pd as $pd){

                    if(Helper::NumberEmpty((int)$pd['pd_physical_stock'])){
                        return $this->jsonErrorResponse($data , "Make Sure Physical Stock is not Empty.");
                    }
                    if(Helper::NumberEmpty((int)$pd['pd_demand_qty'])){
                        return $this->jsonErrorResponse($data , "Make Sure Demand Qty is not Empty.");
                    }

                    $DemandDtl = new TblPurcDemandDtl();
                    if(isset($id) && isset($pd['demand_dtl_id'])){
                        $DemandDtl->demand_id = $id;
                        $DemandDtl->demand_dtl_id = $pd['demand_dtl_id'];
                    }else{
                        $DemandDtl->demand_dtl_id = Utilities::uuid();
                        $DemandDtl->demand_id  = $purchaseDemand->demand_id;
                    }
                    $DemandDtl->sr_no = $sr_no++;
                    $DemandDtl->product_id = $pd['product_id'];
                    $DemandDtl->product_barcode_id = $pd['product_barcode_id'];
                    $DemandDtl->product_barcode_barcode = $pd['pd_barcode'];
                    $DemandDtl->demand_dtl_uom = $pd['uom_id'];
                    $DemandDtl->demand_dtl_packing = $pd['pd_packing'];
                    $DemandDtl->demand_dtl_physical_stock = $pd['pd_physical_stock'];
                    $DemandDtl->demand_dtl_store_stock = $pd['pd_store_stock'];
                    $DemandDtl->demand_dtl_stock_match = $pd['pd_stock_match'];
                    $DemandDtl->demand_dtl_suggest_quantity1 = $pd['pd_suggest_qty_1'];
                    $DemandDtl->demand_dtl_suggest_quantity2 = $pd['pd_suggest_qty_2'];
                    $DemandDtl->demand_dtl_demand_quantity = $pd['pd_demand_qty'];
                    $DemandDtl->demand_dtl_wip_lpo_stock = $pd['pd_wiplpo_stock'];
                    $DemandDtl->demand_dtl_pur_ret_in_waiting = $pd['pd_pur_ret'];
                    $DemandDtl->demand_dtl_entry_status = 1;
                    $DemandDtl->demand_dtl_approve_status = 'pending';
                    $DemandDtl->business_id = auth()->user()->business_id;
                    $DemandDtl->company_id = auth()->user()->company_id;
                    $DemandDtl->branch_id = auth()->user()->branch_id;
                    $DemandDtl->demand_dtl_user_id = auth()->user()->id;
                    $DemandDtl->save();
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


    public function print($id)
    {
        $data['type'] = 'purchase_demand';
        $data['title'] = 'Purchase Demand';
        $data['permission'] = self::$menu_dtl_id.'-print';
        if(isset($id)){
            if(TblPurcDemand::where('demand_id','LIKE',$id)->where(Utilities::currentBCB())->exists()){
                $data['current'] = TblPurcDemand::with('dtls','supplier','salesman')
                    ->where('demand_id',$id)->where('demand_type',$data['type'])->where(Utilities::currentBCB())->first();
            }else{
                abort('404');
            }
        }
        $data['users'] = User::where('id',$data['current']->salesman_id)->where('user_type','erp')->where('user_entry_status',1)->where(Utilities::currentBC())->first();
        return view('prints.purchase_demand_print',compact('data'));
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function destroy($id)
    {
        $type = 'purchase_demand';
        $data = [];
        DB::beginTransaction();
        try{

            $dataExist = TblPurcDemandApprovalDtl::where('demand_id','LIKE',$id)->where(Utilities::currentBCB())->exists();
            if($dataExist === false)
            {
                $purchaseDemand = TblPurcDemand::where('demand_id',$id)->where('demand_type',$type)->where(Utilities::currentBCB())->first();
                $purchaseDemand->dtls()->delete();
                $purchaseDemand->delete();
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
