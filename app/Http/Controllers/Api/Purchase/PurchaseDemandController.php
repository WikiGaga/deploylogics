<?php

namespace App\Http\Controllers\Api\Purchase;

use App\Http\Controllers\ApiController;
use App\Models\TblPurcDemand;
use App\Models\TblPurcDemandDtl;
use App\Models\TblPurcProduct;
use App\Models\TblPurcProductBarcode;
use App\Models\TblPurcProductBarcodeSaleRate;
use App\Models\TblDefiUom;
use App\Models\TblPurcPacking;
use App\Models\TblPurcSupplier;
use App\Models\TblPurcDemandApprovalDtl;
use App\Models\User;
use App\Models\ViewInveDisplayLocation;
use Illuminate\Http\Request;
// db and Validator
use App\Library\ApiUtilities;
use Illuminate\Validation\Rule;
use Validator;
use Illuminate\Support\Facades\DB;
use Exception;
use Session;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Models\ViewPurcGroupItem;
use App\Library\Utilities;
use App\Models\ViewPurcProductBarcode;


class PurchaseDemandController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public static $page_title = 'Purchase Demand';
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request, $id = null)
    {
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

        $data = [];
        $type = 'purchase_demand';
        $data['code_type'] = $type;
        $data['title'] = self::$page_title;
        if(!empty($id)){
            if(TblPurcDemand::where('demand_id','LIKE',$id)->where($currentBCB)->exists()){
                $data['action'] = 'edit';
                $current = TblPurcDemand::with('dtls','supplier','salesman')->where('demand_id',$id)->where('demand_type',$type)->where($currentBCB)->first();
                $data['current'] = $this->FilterData($current);
            }else{
                return $this->ApiJsonErrorResponse($data,'Not Found');
            }
        }else{

            $data['action'] = 'save';
            $doc_data = [
                'biz_type'          => 'branch',
                'model'             => 'TblPurcDemand',
                'code_field'        => 'demand_no',
                'code_prefix'       => strtoupper('d'),
                'code_type_field'   => 'demand_type',
                'code_type'         => $type,
                'business_id'      => $business_id,
                'branch_id'      => $branch_id,
            ];
            $data['document_code'] = ApiUtilities::documentCode($doc_data);
        }

        $data['categories'] = ViewPurcGroupItem::where('group_item_level',3)->orderBy('group_item_name_string')->select('group_item_id','group_item_name_string')->where(Utilities::currentBC())->get();
        // $data['users'] = User::select('id','name')->where('user_type','erp')->where('user_entry_status',1)->where(ApiUtilities::currentBC())->get();

        return $this->ApiJsonSuccessResponse($data,'form data');
    }

    public function getCategoryProducts(Request $request, $group_item_id)
{
    // $business_id = $request->business_id;
    // $branch_id = $request->branch_id;
    $search_key = $request->search_key;

    // $currentBC = [
    //     ['business_id', $business_id],
    //     ['company_id', $business_id]
    // ];
    // $currentBCB = [
    //     ['business_id', $business_id],
    //     ['company_id', $business_id],
    //     ['branch_id', $branch_id]
    // ];

    $query = ViewPurcProductBarcode::where('group_item_id', $group_item_id)
        ->select('product_id', 'product_name', 'PRODUCT_BARCODE_ID', 'PRODUCT_BARCODE_BARCODE', 'UOM_ID',
            'UOM_NAME', 'PRODUCT_BARCODE_PACKING');
        // ->where($currentBCB);

    if (!empty($search_key)) {
        $query->where(function ($subquery) use ($search_key) {
            $subquery->where('product_name', 'like', "%$search_key%")
                ->orWhere('PRODUCT_BARCODE_BARCODE', 'like', "%$search_key%");
        });
    }

    $data['products'] = $query->get();

    return $this->ApiJsonSuccessResponse($data, 'form data');
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
            'category_id' => 'required|numeric',
            'user_id' => 'required|numeric',
        ]);
        if ($validator->fails()) {
            return $this->ApiJsonErrorResponse($data,trans('message.required_fields'));
        }

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

        DB::beginTransaction();
        try{
            if(isset($id)){
                $purchaseDemand = TblPurcDemand::where('demand_id',$id)->where('demand_type',$type)->where($currentBCB)->first();
            }else{
                $purchaseDemand = new TblPurcDemand();
                $purchaseDemand->demand_id = ApiUtilities::uuid();
                $doc_data = [
                'biz_type'          => 'branch',
                'model'             => 'TblPurcDemand',
               'code_field'        => 'demand_no',
                'code_prefix'       => strtoupper('d'),
                'code_type_field'   => 'demand_type',
                'code_type'         => $type,
                'business_id'       => $business_id,
                'branch_id'         => $branch_id,
                 ];
                 $purchaseDemand->demand_no = ApiUtilities::documentCode($doc_data);
            }

            $purchaseDemand->demand_type = $type;
            $purchaseDemand->category_id = $request->category_id;
            $purchaseDemand->demand_date = date('Y-m-d', strtotime($request->demand_date));
            // $purchaseDemand->demand_forward_for_approval = 1;
            $purchaseDemand->salesman_id = $request->user_id;
            $purchaseDemand->demand_notes = isset($request->notes)?$request->notes : "";
            $purchaseDemand->demand_entry_status = 1;
            $purchaseDemand->business_id = $business_id;
            $purchaseDemand->company_id = $business_id;
            $purchaseDemand->branch_id = $branch_id;
            $purchaseDemand->demand_user_id = $request->user_id;
            $purchaseDemand->demand_device_id = "";
        // dd($purchaseDemand , $request->toArray());
            $purchaseDemand->save();

            $del_DemandDtls = TblPurcDemandDtl::where('demand_id',$id)->where($currentBCB)->get();
            foreach ($del_DemandDtls as $del_DemandDtl){
                TblPurcDemandDtl::where('demand_dtl_id',$del_DemandDtl->demand_dtl_id)->where($currentBCB)->delete();
            }

            if(isset($request->pd)){
                $key = 1;
                foreach ($request->pd as $pd){
                    $DemandDtl = new TblPurcDemandDtl();
                    $DemandDtl->demand_dtl_id = ApiUtilities::uuid();
                    $DemandDtl->demand_id  = $purchaseDemand->demand_id;
                    $DemandDtl->product_id = $pd['product_id'];
                    // $DemandDtl->product_name = $pd['product_name'];
                    $DemandDtl->product_barcode_id = $pd['product_barcode_id'];
                    $DemandDtl->product_barcode_barcode = $pd['pd_barcode'];
                    $DemandDtl->demand_dtl_uom = $pd['uom_id'];
                    $DemandDtl->demand_dtl_packing = $pd['pd_packing'];
                    // $DemandDtl->demand_dtl_physical_stock = $pd['pd_physical_stock'];
                    // $DemandDtl->demand_dtl_store_stock = $pd['pd_store_stock'];
                    // $DemandDtl->demand_dtl_stock_match = isset($pd['pd_stock_match'])?$pd['pd_stock_match']:"";
                    // $DemandDtl->demand_dtl_suggest_quantity1 = isset($pd['pd_suggest_qty_1'])?$pd['pd_suggest_qty_1']:"";
                    // $DemandDtl->demand_dtl_suggest_quantity2 = isset($pd['pd_suggest_qty_2'])?$pd['pd_suggest_qty_2']:"";
                    // $DemandDtl->demand_dtl_demand_quantity = 1;
                    // $DemandDtl->demand_dtl_wip_lpo_stock = isset($pd['lop_qty'])?$pd['lop_qty']:"";
                    // $DemandDtl->demand_dtl_pur_ret_in_waiting = isset($pd['purc_return_waiting_qty'])?$pd['purc_return_waiting_qty']:"";
                    $DemandDtl->demand_dtl_entry_status = 1;
                    $DemandDtl->demand_dtl_approve_status = 'pending';
                    $DemandDtl->business_id = $business_id;
                    $DemandDtl->company_id = $business_id;
                    $DemandDtl->branch_id = $branch_id;
                    $DemandDtl->demand_dtl_user_id = $user_id;
                    $DemandDtl->sr_no = $key++;;
                    $DemandDtl->save();
                }
            }

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
            return $this->ApiJsonSuccessResponse($data,trans('message.update'));
        }else{
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

    public function FilterData($current){
        $pd_dtl = [];
        $i=0;
        foreach($current->dtls as $dtl){
            $pd_dtl[$i]["demand_dtl_id"] = $dtl->demand_dtl_id;
            $pd_dtl[$i]["demand_id"] = $dtl->demand_id;
            $pd_dtl[$i]["product_id"] = $dtl->product->product_id;
            $pd_dtl[$i]["product_name"] = $dtl->product->product_name;
            $pd_dtl[$i]["product_barcode_id"] = $dtl->barcode->product_barcode_id;
            $pd_dtl[$i]["pd_barcode"] = $dtl->barcode->product_barcode_barcode;
            $pd_dtl[$i]["uom_id"] = $dtl->uom->uom_id;
            $pd_dtl[$i]["uom_name"] = $dtl->uom->uom_name;
            $pd_dtl[$i]["pd_packing"] = $dtl->barcode->product_barcode_packing;
            $pd_dtl[$i]["pd_physical_stock"] = ($dtl->demand_dtl_physical_stock != null)?$dtl->demand_dtl_physical_stock:"";
            $pd_dtl[$i]["store_stock"] = ($dtl->demand_dtl_store_stock != null)?$dtl->demand_dtl_store_stock:"";
            $pd_dtl[$i]["demand_quantity"] = ($dtl->demand_dtl_demand_quantity != null)?$dtl->demand_dtl_demand_quantity:"";
            $pd_dtl[$i]["pd_suggest_qty_1"] = ($dtl->demand_dtl_suggest_quantity1 != null)?$dtl->demand_dtl_suggest_quantity1:"";
            $pd_dtl[$i]["pd_suggest_qty_2"] = ($dtl->demand_dtl_suggest_quantity2 != null)?$dtl->demand_dtl_suggest_quantity2:"";
            $pd_dtl[$i]["lop_qty"] = ($dtl->demand_dtl_wip_lpo_stock != null)?$dtl->demand_dtl_wip_lpo_stock:"";
            $pd_dtl[$i]["purc_return_waiting_qty"] = ($dtl->demand_dtl_pur_ret_in_waiting != null)?$dtl->demand_dtl_pur_ret_in_waiting:"";

            $i++;
        }

        $object = (object) [
            "business_id" 		=> 	$current->business_id,
            "branch_id" 		=> 	$current->branch_id,
            "user_id" 			=> 	$current->salesman_id,
            "demand_id"         =>  $current->demand_id,
            "demand_code"          =>  $current->demand_no,
            "demand_date"          =>  date('d-m-Y', strtotime(trim(str_replace('/','-',$current->demand_date)))),
            "supplier_id" 		=> 	isset($current->supplier->supplier_id)?$current->supplier->supplier_id:'',
            "supplier_name" 	=> 	isset($current->supplier->supplier_name)?$current->supplier->supplier_name:'',
            "notes" 			=> 	$current->demand_notes,
            "pd"                =>  $pd_dtl,
        ];

        return $object;
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

            $dataExist = TblPurcDemandApprovalDtl::where('demand_id','LIKE',$id)->exists();
            if($dataExist === false)
            {
                $purchaseDemand = TblPurcDemand::where('demand_id',$id)->where('demand_type',$type)->first();
                $purchaseDemand->dtls()->delete();
                $purchaseDemand->delete();
            }else{
                return $this->ApiJsonErrorResponse($data, trans('message.not_delete'));
            }

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
