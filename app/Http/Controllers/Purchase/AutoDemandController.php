<?php

namespace App\Http\Controllers\Purchase;

use App\Models\TblInveStock;
use App\Models\TblInveStockDtl;
use App\Models\TblPurcDemandDtl;
use App\Models\TblPurcProductBarcode;
use App\Models\TblPurcProductBarcodeDtl;
use App\Models\TblPurcProductBarcodePurchRate;
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
    public static $page_title = 'Purchase Demand';
    public static $redirect_url = 'auto-demand';
    public static $menu_dtl_id = '220';

    function create($id = null)
    {
        $product_list = [];
        $product_id = [];
        $data['page_data'] = [];
        $data['form_type'] = 'auto_demand';
        $data['menu_id'] = self::$menu_dtl_id;
        $data['page_data']['title'] = self::$page_title;
        $data['page_data']['path_index'] = $this->prefixIndexPage . self::$redirect_url;
        $data['page_data']['create'] = '/' . self::$redirect_url . $this->prefixCreatePage;
        $data['current_branch'] = auth()->user()->branch_id;
        $type = 'purchase_demand';

        if (isset($id)) {
            if (TblPurcDemand::where('demand_id', $id)->where('demand_type', $type)->where(Utilities::currentBCB())->exists()) {
                $data['page_data'] = array_merge($data['page_data'], Utilities::editForm());
                $data['permission'] = self::$menu_dtl_id . '-edit';
                $data['id'] = $id;
                $data['current'] = TblPurcDemand::where('demand_id', $id)->where('demand_type', $type)->where(Utilities::currentBCB())->with('dtls')->first();

//                $data['page_data']['print'] = '/' . self::$redirect_url . '/print/' . $id;

            } else {
                abort('404');
            }
        } else {
            $data['permission'] = self::$menu_dtl_id . '-create';
            $data['page_data'] = array_merge($data['page_data'], Utilities::newForm());
            $doc_data = [
                'biz_type' => 'branch',
                'model' => 'TblPurcDemand',
                'code_field' => 'demand_no',
                'code_prefix' => strtoupper('d'),
                'code_type_field' => 'demand_type',
                'code_type' => $type,
            ];
            $data['document_code'] = Utilities::documentCode($doc_data);
        }

        $data['store'] = TblDefiStore::where(Utilities::currentBCB())->get();
        $data['branches'] = TblSoftBranch::orderBy('branch_name')->get();
        $data['categories'] = ViewPurcGroupItem::where('group_item_level', 3)->orderBy('group_item_name_string')->select('group_item_id', 'group_item_name_string')->where(Utilities::currentBC())->get();

        return view('purchase.auto-demand.form', compact('data', 'product_list'));
    }

    public function store(Request $request, $id = null)
    {
//         dd($request->all());
        if (empty($request->pd)) {
            return $this->returnjsonerror("Please Enter Product Detail", 201);
        }
        $data = [];
        $type = 'purchase_demand';
        $validator = Validator::make($request->all(), [
            'category_id' => 'required|numeric',
        ]);
        if ($validator->fails()) {
            $data['validator_errors'] = $validator->errors();
            return $this->jsonErrorResponse($data, trans('message.required_fields'), 422);
        }
        DB::beginTransaction();
        try {
            if (isset($id)) {
                $purchaseDemand = TblPurcDemand::where('demand_id', $id)->where('demand_type', $type)->where(Utilities::currentBCB())->first();

            } else {
                $purchaseDemand = new TblPurcDemand();
                $purchaseDemand->demand_id = Utilities::uuid();
                $doc_data = [
                    'biz_type' => 'branch',
                    'model' => 'TblPurcDemand',
                    'code_field' => 'demand_no',
                    'code_prefix' => strtoupper('d'),
                    'code_type_field' => 'demand_type',
                    'code_type' => $type,
                ];
                $purchaseDemand->demand_no = Utilities::documentCode($doc_data);
                $purchaseDemand->demand_type = $type;
                $purchaseDemand->salesman_id = auth()->user()->id;
                $purchaseDemand->demand_entry_status = 1;
                $purchaseDemand->branch_id = auth()->user()->branch_id;
                $purchaseDemand->business_id = auth()->user()->business_id;
                $purchaseDemand->company_id = auth()->user()->company_id;
                $purchaseDemand->demand_user_id = auth()->user()->id;
                $purchaseDemand->demand_device_id = "";
            }
            $purchaseDemand->demand_date = date('Y-m-d', strtotime($request->demand_date));
            $purchaseDemand->category_id = $request->category_id;
            $purchaseDemand->demand_notes = isset($request->demand_notes) ? $request->demand_notes : "";
            $purchaseDemand->save();

            $form_id = $purchaseDemand->demand_id;

            $del_DemandDtls = TblPurcDemandDtl::where('demand_id', $id)->where(Utilities::currentBCB())->get();
            foreach ($del_DemandDtls as $del_DemandDtl) {
                TblPurcDemandDtl::where('demand_dtl_id', $del_DemandDtl->demand_dtl_id)->delete();
            }

            if (isset($request->pd)) {
                $key = 1;
                foreach ($request->pd as $pd) {
                    $DemandDtl = new TblPurcDemandDtl();
                    $DemandDtl->demand_dtl_id = Utilities::uuid();
                    $DemandDtl->demand_id = $purchaseDemand->demand_id;
                    $DemandDtl->product_id = $pd['product_id'];
                    $DemandDtl->product_barcode_id = $pd['product_barcode_id'];
                    $DemandDtl->product_barcode_barcode = $pd['pd_barcode'];
                    $DemandDtl->demand_dtl_uom = $pd['uom_id'];
                    $DemandDtl->demand_dtl_packing = $pd['pd_packing'];
                    $DemandDtl->demand_dtl_entry_status = 1;
                    $DemandDtl->demand_dtl_approve_status = 'pending';
                    $DemandDtl->business_id = auth()->user()->business_id;
                    $DemandDtl->company_id = auth()->user()->business_id;
                    $DemandDtl->branch_id = auth()->user()->branch_id;
                    $DemandDtl->demand_dtl_user_id = auth()->user()->id;
                    $DemandDtl->sr_no = $key++;;
                    $DemandDtl->save();
                }
            }

        } catch (QueryException $e) {
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
        if (isset($id)) {
            $data = array_merge($data, Utilities::returnJsonEditForm());
            $data['redirect'] = $this->prefixIndexPage . self::$redirect_url;
            return $this->jsonSuccessResponse($data, trans('message.update'), 200);
        } else {
            // Triger New Job For Long Work
//            TrigerAutoDemandProcedure::dispatch($form_id)->delay(now()->addMinutes(1));

            $data = array_merge($data, Utilities::returnJsonNewForm());
            $data['redirect'] = '/' . self::$redirect_url . $this->prefixCreatePage . '/' . $form_id;
            $data['form_id'] = $form_id;
            return $this->jsonSuccessResponse($data, trans('message.create'), 200);
        }
    }

    public function loadInnerGrid(Request $request, $id = null)
    {
        $data = [];
        $data['requestBranchs'] = TblPurcAutoDemandRequest::select('ad_id', 'product_id', 'product_barcode_id', 'product_unit_id', 'extra_qty', 'approve_qty', 'req_branch_id', 'stock_request_id')->with('product', 'barcode', 'uom')->where('ad_id', $id)->distinct('product_id')->orderBy('product_id')->get();
        $data['branches'] = TblSoftBranch::orderBy('branch_name')->get();
        return $this->jsonSuccessResponse($data, 'Data is loaded.', 200);
    }

    public function print($id)
    {
        $data['type'] = 'auto_demand';
        $data['title'] = 'Purchase Auto Demand';
        $data['permission'] = self::$menu_dtl_id . '-print';
        if (isset($id)) {
            if (TblPurcAutoDemand::where('ad_id', 'LIKE', $id)->where(Utilities::currentBCB())->exists()) {
                $query = "SELECT D.REORDER_QTY,B.BRANCH_SHORT_NAME,AD.BRANCH_ID,D.IS_APPROVE,AD.AD_CODE,AD.AD_DATE,AD.AD_TYPE,BR.UOM_NAME,BR.PRODUCT_BARCODE_BARCODE,BR.PRODUCT_NAME,D.PRODUCT_ID,
                    D.PRODUCT_BARCODE_ID,D.PRODUCT_BARCODE_PACKING PACKING,D.PRODUCT_UNIT_ID UOM_ID,
                    SUM(D.APPROVE_QTY) QTY,SUM(D.STOCK_QTY) STOCK_QTY,SUM(D.TOTAL_CONSUMPTION_QTY) CONSUMPTION_QTY,SUM(D.FOC_QTY) FOC_QTY,DECODE(SUM(D.APPROVE_QTY),0,0,((SUM(D.AMOUNT)/SUM(D.APPROVE_QTY)))) AS RATE,
                    SUM(D.AMOUNT) AMOUNT,AVG(D.VAT) VAT_AMOUNT,SUM(D.TOT_AMOUNT) GROSS_AMOUNT,
                    AVG(D.VAT_PERC) VAT_PERC, SUM(D.DISC_PERC) DIS_PERC, SUM(D.DISC) DISC FROM
                    TBL_PURC_AUTO_DEMAND_DTL D
                    JOIN TBL_PURC_AUTO_DEMAND AD ON AD.AD_ID = D.AD_ID
                    JOIN TBL_SOFT_BRANCH B ON B.BRANCH_ID = AD.BRANCH_ID
                    JOIN VW_PURC_PRODUCT_BARCODE_FIRST BR ON BR.PRODUCT_ID = D.PRODUCT_ID
                    WHERE D.AD_ID = " . $id . " GROUP BY
                    D.REORDER_QTY,B.BRANCH_SHORT_NAME,AD.BRANCH_ID,D.IS_APPROVE,AD.AD_CODE,AD.AD_DATE,AD.AD_TYPE,BR.UOM_NAME,BR.PRODUCT_BARCODE_BARCODE,BR.PRODUCT_NAME,D.PRODUCT_ID,D.PRODUCT_BARCODE_ID,D.PRODUCT_BARCODE_PACKING,D.PRODUCT_UNIT_ID ORDER BY BR.PRODUCT_NAME ASC";
                $data['current'] = DB::select($query);
            } else {
                abort('404');
            }
        }

        return view('prints.purchase_auto_demand_print', compact('data'));
    }

    function triggerAutoDemandProcedure($id = null)
    {
        if (isset($id)) {
            $pdo = DB::getPdo();
            $ad_id = $id;
            $stmt = $pdo->prepare("begin " . Utilities::getDatabaseUsername() . ".PRO_AUTO_DEMAND(:p1); end;");
            $stmt->bindParam(':p1', $ad_id);
            $stmt->execute();
        }

        return $this->jsonSuccessResponse([], 'Procedure Called', 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $data = [];
        DB::beginTransaction();
        try {
            $dataExist = TblPurcDemand::where('demand_id', 'Like', $id)->where(Utilities::currentBCB())->exists();
            if ($dataExist === true) {
                $ad = TblPurcDemand::where('ad_id', $id)->where(Utilities::currentBCB())->first();
                $ad->dtls()->delete();
                $ad->delete();
            } else {
                return $this->jsonErrorResponse($data, trans('message.not_delete'), 200);
            }

        } catch (QueryException $e) {
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
