<?php

namespace App\Http\Controllers\Purchase;

use Exception;
use Validator;
use App\Library\Utilities;
use App\Models\TblDefiStore;
use Illuminate\Http\Request;
use App\Models\TblAutoDemand;
use App\Models\TblPurcDemand;
use App\Models\TblSoftBranch;
use App\Models\TblPurcSupplier;
use App\Models\TblAutoDemandDtl;
use App\Models\ViewPurcGroupItem;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\Queue\Queue;
use App\Models\ViewPurcDemandApproval;
use App\Jobs\TrigerAutoDemandProcedure;
use App\Models\TblPurcAutoDemand;
use App\Models\ViewInveDisplayLocation;
use Illuminate\Database\QueryException;
use App\Models\TblPurcAutoDemandCriteria;
use App\Models\TblPurcAutoDemandRequest;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class AutoDemandController extends Controller
{
    public static $page_title = 'Auto Purchase Demand';
    public static $redirect_url = 'auto-demand';
    public static $menu_dtl_id = '220';

    function create($id = null){
        $product_list = [];$product_id=[];
        $data['page_data'] = [];
        $data['form_type'] = 'auto_demand';
        $data['menu_id'] = self::$menu_dtl_id;
        $data['page_data']['title'] = self::$page_title;
        $data['page_data']['path_index'] = $this->prefixIndexPage.self::$redirect_url;
        $data['page_data']['create'] = '/'.self::$redirect_url.$this->prefixCreatePage;
        $data['current_branch'] = auth()->user()->branch_id;
        if(isset($id)){
            if(TblAutoDemand::where('ad_id','LIKE',$id)->where(Utilities::currentBCB())->exists()){
                $data['page_data'] = array_merge($data['page_data'], Utilities::editForm());
                $data['permission'] = self::$menu_dtl_id.'-edit';
                $data['id'] = $id;
                $data['current'] = TblAutoDemand::with('dtl')->where('ad_id',$id)->where(Utilities::currentBCB())->first();

                $data['document_code'] = $data['current']->ad_code;
                $data['page_data']['print'] = '/'.self::$redirect_url.'/print/'.$id;

                if(count($data['current']->dtl) > 0){
                    foreach ($data['current']->dtl as $item) {
                        if(!key_exists($item->product_id , $product_list)){
                            $product_list[$item->product_id][] = $item;
                        }else{
                            $product_list[$item->product_id][] = $item;
                        }
                    }
                }
            }else{
                abort('404');
            }
        }else{
            $data['permission'] = self::$menu_dtl_id.'-create';
            $data['page_data'] = array_merge($data['page_data'], Utilities::newForm());
            $doc_data = [
                'biz_type'          => 'branch',
                'model'             => 'TblPurcAutoDemand',
                'code_field'        => 'ad_code',
                'code_prefix'       => strtoupper('AD'),
                'code_type_field'   => 'code_type',
                'code_type'         => strtoupper('AD'),
            ];
            $data['document_code'] = Utilities::documentCode($doc_data);
        }

        if(isset($id)){
            $data['demands'] = '';
            $selectedDemands = isset($data['current']->demand_id) ?$data['current']->demand_id : [];
            foreach ($selectedDemands as $value) {
                $name = TblPurcDemand::where('demand_id' , $value)->select('demand_no')->first();
                if(isset($name)){
                    $data['demands'] .= "<option value='".$value."' selected> ". $name->demand_no ." </option>";
                }
            }
            $data['suppliers'] = '';
            $selectedSuppliers = isset($data['current']->supplier_id) ? $data['current']->supplier_id : [];
            foreach ($selectedSuppliers as $value) {
                $name = TblPurcSupplier::where('supplier_id' , $value)->select('supplier_name')->first();
                if(isset($name)){
                    $data['suppliers'] .= "<option value='".$value."' selected> ". $name->supplier_name ." </option>";
                }
            }
            $data['display_location'] = '';
            $selectedLocations = isset($data['current']->location_id) ? $data['current']->location_id : [];
            foreach ($selectedLocations as $value) {
                $name = ViewInveDisplayLocation::where('display_location_id' , $value)->select('display_location_name_string')->first();
                if(isset($name)){
                    $data['display_location'] .= "<option value='".$value."' selected> ". $name->display_location_name_string ." </option>";
                }
            }
            $data['group_item'] = '';
            $selectedGroups = isset($data['current']->group_id) ? $data['current']->group_id : [];
            foreach ($selectedGroups as $value) {
                $name = ViewPurcGroupItem::where('group_item_id' , $value)->select('group_item_name_string')->first();
                if(isset($name)){
                    $data['group_item'] .= "<option value='".$value."' selected> ". $name->group_item_name_string ." </option>";
                }
            }
            $data['consumption_branches'] = '';
            $selectedConsuption = isset($data['current']->consumption_branch_id) ? $data['current']->consumption_branch_id : [];
            foreach ($selectedConsuption as $value) {
                $name = TblSoftBranch::where('branch_id' , $value)->select('branch_name')->first();
                if(isset($name)){
                    $data['consumption_branches'] .= "<option value='".$value."' selected> ". $name->branch_name ." </option>";
                }
            }
            $data['other_branches'] = '';
            $selectedConsuption = isset($data['current']->suggest_stock_request_branch) ? $data['current']->suggest_stock_request_branch : [];
            foreach ($selectedConsuption as $value) {
                $name = TblSoftBranch::where('branch_id' , $value)->select('branch_name')->first();
                if(isset($name)){
                    $data['other_branches'] .= "<option value='".$value."' selected> ". $name->branch_name ." </option>";
                }
            }
        }else{
            $data['store'] = TblDefiStore::where('store_entry_status',1)->where(Utilities::currentBCB())->get();
            $data['demands'] = TblPurcDemand::where('demand_type','purchase_demand')->orderby('demand_no')->get();
            $data['suppliers'] = TblPurcSupplier::get();
            $data['display_location'] = ViewInveDisplayLocation::orderBy('display_location_name_string')->get();
            $data['group_item'] = ViewPurcGroupItem::orderBy('group_item_name_string')->where(Utilities::currentBC())->get();
        }
        $data['branches'] = TblSoftBranch::orderBy('branch_name')->get();

        return view('purchase.auto-demand.form' , compact('data','product_list'));
    }

    public function store(Request $request , $id = null){
        // dd($request->toArray());
        $data = [];
        $validator = Validator::make($request->all(), [

        ]);
        if ($validator->fails()) {
            $data['validator_errors'] = $validator->errors();
            return $this->jsonErrorResponse($data, trans('message.required_fields'), 422);
        }
        DB::beginTransaction();
        try{
            if(isset($id)){
                $autoDemand = TblAutoDemand::where('ad_id',$id)->where(Utilities::currentBCB())->first();
                $autoDemand->create_stock_requests = isset($request->create_stock_request) ? 1 : "";
                $autoDemand->save();
            }else{
                $autoDemand = new TblAutoDemand();
                $autoDemand->ad_id = Utilities::uuid();
                $doc_data = [
                    'biz_type'          => 'branch',
                    'model'             => 'TblAutoDemand',
                    'code_field'        => 'ad_code',
                    'code_prefix'       => strtoupper('AD'),
                    'code_type_field'   => 'code_type',
                ];
                $autoDemand->ad_code = Utilities::documentCode($doc_data);
                $demands = isset($request->demands) ? implode("," , $request->demands) : "";
                $suppliers = isset($request->suppliers) ? implode("," , $request->suppliers) : "";
                $productGroups = isset($request->product_groups) ? implode("," , $request->product_groups) : "";
                $locations = isset($request->locations) ? implode("," , $request->locations) : "";
                if(isset($request->consumption_type) && count($request->consumption_type) == 2){
                    $consumptionType = 'ALL';
                }else{
                    $consumptionType = isset($request->consumption_type) ? implode("," , $request->consumption_type) : "";
                }

                $consumptionBranches = isset($request->consumption_branches) ? implode("," , $request->consumption_branches) : "";
                $suggestStockBranches = isset($request->suggest_request_branches) ? implode("," , $request->suggest_request_branches) : "";
                $leadDate = strtotime($request->lead_date);
                $adDate = strtotime($request->ad_date);
                $leadDays = (int)(($leadDate - $adDate) / (60 * 60 * 24));
                // Save Data Into the Database
                $autoDemand->code_type = 'AD';
                $autoDemand->demand_id = $demands;
                $autoDemand->supplier_id = $suppliers;
                $autoDemand->group_id = $productGroups;
                $autoDemand->location_id = $locations;
                $autoDemand->ad_type = $request->ad_type;
                $autoDemand->priority_check = $request->priority_check;
                $autoDemand->consumption_type = $consumptionType;
                $autoDemand->consumption_base = $request->consumption_base;
                $autoDemand->consumption_days = $request->consumption_days;
                $autoDemand->consumption_start_date = isset($request->consumption_start_date) ? date('Y-m-d' , strtotime($request->consumption_start_date)) : date('Y-m-d' , time());
                $autoDemand->consumption_end_date = isset($request->consumption_end_date) ? date('Y-m-d' , strtotime($request->consumption_end_date)) : date('Y-m-d' , time());
                $autoDemand->consumption_branch_id = $consumptionBranches;
                $autoDemand->lead_days = $leadDays;
                $autoDemand->lead_date = date('Y-m-d' , strtotime($request->lead_date));
                $autoDemand->ad_date = date('Y-m-d' , strtotime($request->ad_date));
                $autoDemand->suggest_stock_request = isset($request->suggesstockrequest) ? 1 : "";
                $autoDemand->suggest_stock_request_lead_days = isset($request->suggestion_lead_days) ? $request->suggestion_lead_days : '';
                $autoDemand->suggest_stock_request_branch = $suggestStockBranches;
                $autoDemand->create_stock_requests = isset($request->create_stock_request) ? 1 : "";
                $autoDemand->create_user_id = auth()->user()->id;
                $autoDemand->branch_id = auth()->user()->branch_id;
                $autoDemand->business_id = auth()->user()->business_id;
                $autoDemand->company_id = auth()->user()->company_id;
                $autoDemand->ad_status = 0; // Pending
                $autoDemand->save();

                // Saving the Criterias
                $criteria = [];
                $criteria['DEMAND'] = $request->demands;
                $criteria['SUPPLIER'] = $request->suppliers;
                $criteria['PRODUCT_GROUP'] = $request->product_groups;
                $criteria['LOCATIONS'] = $request->locations;
                $criteria['CONSUMPTION_BRANCH'] = $request->consumption_branches;
                $criteria['SUGGEST_BRANCH'] = $request->suggest_request_branches;
                //Saving Critera Fields In Criteria Table AS WELL
                foreach($criteria as $key => $value){
                    if(!is_null($value)){
                        foreach ($value as $val) {
                            DB::table('tbl_purc_auto_demand_criteria')->insert([
                                'ad_id'     => $autoDemand->ad_id,
                                'cr_id'     => $val,
                                'cr_type'   => $key
                            ]);
                        }
                    }
                }
            }

            $form_id = $autoDemand->ad_id;
            // Update The Rows With New Values
            if(isset($id)){
                TblAutoDemandDtl::where('ad_id' , $id)->delete();
            }
            if(isset($request->pd)){
                foreach ($request->pd as $summary) {
                    if(isset($summary['sub'])){
                        foreach ($summary['sub'] as $key => $value) {
                            $detl = new TblAutoDemandDtl();
                            $detl->AD_ID                      = $id;
                            $detl->AD_DTL_ID                  = Utilities::uuid();
                            $detl->PRODUCT_ID                 = $value['product_id'];
                            $detl->PRODUCT_BARCODE_ID         = $value['product_barcode_id'];
                            $detl->DEMAND_ID                  = $value['demand_id'];
                            $detl->STOCK_QTY                  = $value['stock_qty'];
                            $detl->PHYSICAL_STOCK             = $value['physical_stock'];
                            $detl->TRANSFER_QTY               = isset($value['tansfer_qty']) ? $value['tansfer_qty'] : 0;
                            $detl->SALE_QTY                   = isset($value['sale_qty']) ? $value['sale_qty'] : 0;
                            $detl->TOTAL_CONSUMPTION_QTY      = isset($value['consumption_qty']) ? $value['consumption_qty'] : 0;
                            $detl->MAX_QTY                    = $value['max_qty'];
                            $detl->REORDER_QTY                = $value['reorder_qty'];
                            $detl->EXPECTED_CONSUMPTION_QTY   = $value['exp_cons_qty'];
                            $detl->SUPPLIER_ID                = $value['supplier_id'];
                            $detl->DEMAND_QTY                 = $value['demand_qty'];
                            $detl->FOC_QTY                    = isset($value['foc_qty']) ? $value['foc_qty'] : 0;
                            $detl->DEMAND_PACKING             = $value['pd_packing'];
                            $detl->DEMAND_BASE_QTY            = $value['demand_base_qty'];
                            $detl->PUR_RATE                   = $value['rate'];
                            $detl->AMOUNT                     = $value['amount'];
                            $detl->DISC_PERC                  = $value['dis_perc'];
                            $detl->DISC                       = $value['dis_amount'];
                            $detl->VAT_PERC                   = $value['vat_perc'];
                            $detl->VAT                        = $value['vat_amount'];
                            $detl->TOT_AMOUNT                 = isset($value['gross_amount']) ? $value['gross_amount'] : 0;
                            $detl->DEMAND_BRANCH_ID           = $value['demand_branch_id'];
                            $detl->SALE_RATE                  = $value['sale_rate'];
                            $detl->LOWEST_PUR_RATE            = $value['low_purc_rate'];
                            $detl->LOWEST_RATE_DATE           = date('Y-m-d' , strtotime($value['low_purc_date']));
                            $detl->PRODUCT_BARCODE_PACKING    = $value['pd_packing'];
                            $detl->PRODUCT_UNIT_ID            = $value['uom_id'];
                            $detl->APPROVE_QTY                = $value['quantity'];
                            $detl->SUGGEST_QTY_REORDER        = $value['suggest_qty_reorder'];
                            $detl->SUGGEST_QTY_CONSUMPTION    = $value['suggest_qty_consumption'];
                            $detl->IS_APPROVE                 = isset($value['action']) ? $value['action'] : 'pending';
                            $detl->save();
                        }
                    }
                }
            }

            // Update The Extra Qty & Approve Qty
            // dd($request->req);
            if(isset($request->req)){
                foreach ($request->req as $branch => $item) {
                    foreach ($item as $value) {
                        $reqBranch = TblPurcAutoDemandRequest::where('req_branch_id' , $branch)
                            ->where('ad_id' , $value['ad_id'])
                            ->where('product_id' , $value['product_id'])
                            ->first();
                        $reqBranch->approve_qty = $value['aprv_qty'];
                        $reqBranch->save();
                    }
                }
            }
            // Create Stock Request(s)
            if(isset($request->req) && isset($request->create_stock_request)){
                foreach ($request->req as $branch => $item) {
                    $item = TblPurcAutoDemandRequest::where('req_branch_id' , $branch)
                        ->where('ad_id' , $item[0]['ad_id'])
                        ->where('product_id' , $item[0]['product_id'])
                        ->first('stock_request_id');
                    if(isset($item->stock_request_id)){
                        $stockRequest = TblPurcDemand::where('demand_id',$item->stock_request_id)->where('demand_type','stock_request')->first();
                    }else{
                        $stockRequest = new TblPurcDemand();
                        $stockRequest->demand_id = Utilities::uuid();
                        $doc_data = [
                            'biz_type'          => 'branch',
                            'model'             => 'TblPurcDemand',
                            'code_field'        => 'demand_no',
                            'code_prefix'       => strtoupper('SDR'),
                            'code_type_field'   => 'demand_type',
                            'code_type'         => 'stock_request',
                        ];
                    }
                }

                // $document_code = Utilities::documentCode($doc_data);
                // $stockRequest->demand_no = $document_code;
                // $stockRequest->demand_type = 'stock_request';
                // $stockRequest->demand_date = date('Y-m-d', time());
                // $stockRequest->demand_forward_for_approval = 1;
                // $stockRequest->demand_branch_to = $branch;
                // $stockRequest->demand_entry_status = 1;
                // $stockRequest->business_id = auth()->user()->business_id;
                // $stockRequest->company_id = auth()->user()->company_id;
                // $stockRequest->branch_id = auth()->user()->branch_id;
                // $stockRequest->demand_user_id = auth()->user()->id;
                // $stockRequest->save();
                // foreach ($item as $value) {
                //     $reqBranch = TblPurcAutoDemandRequest::where('req_branch_id' , $branch)
                //     ->where('ad_id' , $value['ad_id'])
                //     ->where('product_id' , $value['product_id'])
                //     ->first();
                //     $reqBranch->approve_qty = $value['aprv_qty'];
                //     $reqBranch->save();
                // }
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
            $data['redirect'] = $this->prefixIndexPage.self::$redirect_url;
            return $this->jsonSuccessResponse($data, trans('message.update'), 200);
        }else{
            // Triger New Job For Long Work
            TrigerAutoDemandProcedure::dispatch($form_id)->delay(now()->addMinutes(1));

            $data = array_merge($data, Utilities::returnJsonNewForm());
            $data['redirect'] = '/'.self::$redirect_url.$this->prefixCreatePage.'/'.$form_id;
            $data['form_id'] = $form_id;
            return $this->jsonSuccessResponse($data, trans('message.create'), 200);
        }
    }

    public function loadInnerGrid(Request $request , $id = null){
        $data = [];
        $data['requestBranchs'] = TblPurcAutoDemandRequest::select('ad_id','product_id','product_barcode_id','product_unit_id','extra_qty','approve_qty','req_branch_id','stock_request_id')->with('product','barcode','uom')->where('ad_id',$id)->distinct('product_id')->orderBy('product_id')->get();
        $data['branches'] = TblSoftBranch::orderBy('branch_name')->get();
        return $this->jsonSuccessResponse($data , 'Data is loaded.', 200);
    }

    public function print($id)
    {
        $data['type'] = 'auto_demand';
        $data['title'] = 'Purchase Auto Demand';
        $data['permission'] = self::$menu_dtl_id.'-print';
        if(isset($id)){
            if(TblPurcAutoDemand::where('ad_id','LIKE',$id)->where(Utilities::currentBCB())->exists()){
                $query = "SELECT D.REORDER_QTY,B.BRANCH_SHORT_NAME,AD.BRANCH_ID,D.IS_APPROVE,AD.AD_CODE,AD.AD_DATE,AD.AD_TYPE,BR.UOM_NAME,BR.PRODUCT_BARCODE_BARCODE,BR.PRODUCT_NAME,D.PRODUCT_ID,
                    D.PRODUCT_BARCODE_ID,D.PRODUCT_BARCODE_PACKING PACKING,D.PRODUCT_UNIT_ID UOM_ID,
                    SUM(D.APPROVE_QTY) QTY,SUM(D.STOCK_QTY) STOCK_QTY,SUM(D.TOTAL_CONSUMPTION_QTY) CONSUMPTION_QTY,SUM(D.FOC_QTY) FOC_QTY,DECODE(SUM(D.APPROVE_QTY),0,0,((SUM(D.AMOUNT)/SUM(D.APPROVE_QTY)))) AS RATE,
                    SUM(D.AMOUNT) AMOUNT,AVG(D.VAT) VAT_AMOUNT,SUM(D.TOT_AMOUNT) GROSS_AMOUNT,
                    AVG(D.VAT_PERC) VAT_PERC, SUM(D.DISC_PERC) DIS_PERC, SUM(D.DISC) DISC FROM
                    TBL_PURC_AUTO_DEMAND_DTL D
                    JOIN TBL_PURC_AUTO_DEMAND AD ON AD.AD_ID = D.AD_ID
                    JOIN TBL_SOFT_BRANCH B ON B.BRANCH_ID = AD.BRANCH_ID
                    JOIN VW_PURC_PRODUCT_BARCODE_FIRST BR ON BR.PRODUCT_ID = D.PRODUCT_ID
                    WHERE D.AD_ID = ".$id." GROUP BY
                    D.REORDER_QTY,B.BRANCH_SHORT_NAME,AD.BRANCH_ID,D.IS_APPROVE,AD.AD_CODE,AD.AD_DATE,AD.AD_TYPE,BR.UOM_NAME,BR.PRODUCT_BARCODE_BARCODE,BR.PRODUCT_NAME,D.PRODUCT_ID,D.PRODUCT_BARCODE_ID,D.PRODUCT_BARCODE_PACKING,D.PRODUCT_UNIT_ID ORDER BY BR.PRODUCT_NAME ASC";
                $data['current'] = DB::select($query);
            }else{
                abort('404');
            }
        }

        return view('prints.purchase_auto_demand_print',compact('data'));
    }

    function triggerAutoDemandProcedure($id = null){
        if(isset($id)){
            $pdo = DB::getPdo();
            $ad_id = $id;
            $stmt = $pdo->prepare("begin ".Utilities::getDatabaseUsername().".PRO_AUTO_DEMAND(:p1); end;");
            $stmt->bindParam(':p1', $ad_id);
            $stmt->execute();
        }

        return $this->jsonSuccessResponse([], 'Procedure Called' , 200);
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
            $dataExist = TblAutoDemand::where('ad_id','Like',$id)->where(Utilities::currentBCB())->exists();
            if($dataExist === true)
            {
                $ad = TblAutoDemand::where('ad_id',$id)->where(Utilities::currentBCB())->first();
                $ad->dtl()->delete();
                $ad->delete();
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
