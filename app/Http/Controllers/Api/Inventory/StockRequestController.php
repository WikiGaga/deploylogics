<?php

namespace App\Http\Controllers\Api\Inventory;

use App\Http\Controllers\ApiController;
use App\Models\TblPurcDemand;
use App\Models\TblPurcDemandDtl;
use App\Models\TblSoftBranch;
use App\Models\User;
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


class StockRequestController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public static $page_title = 'Stock Request';
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
        $type = 'stock_request';
        $data['title'] = self::$page_title;
        if(isset($id)){
            if(TblPurcDemand::where('demand_id','LIKE',$id)->exists()){
                $data['action'] = 'edit';
                $data['current'] = TblPurcDemand::with('dtls','supplier')->where('demand_id',$id)->where('demand_type',$type)->first();
            }else{
                abort('404');
            }
        }else{
            $data['action'] = 'save';
            $max_no = TblPurcDemand::where('demand_type',$type)->max('demand_no');
            $data['document_code'] = $this->documentCode($max_no,'SDR');
        }
        $data['branch'] = TblSoftBranch::select('branch_id','branch_short_name')->get();
        $data['users'] = User::select('id','name')->where('user_type','erp')->where('user_entry_status',1)->where(ApiUtilities::currentBC())->get();
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
        $type = 'stock_request';
        $data = [];
        $validator = Validator::make($request->all(), [
            'supplier_id' => 'nullable|numeric',
            'demand_branch_to' => 'nullable|numeric',
        ]);
        if ($validator->fails()) {
            return $this->ApiJsonErrorResponse($data,trans('message.required_fields'));
        }
        DB::beginTransaction();
        try{
            if(isset($id)){
                $purchaseDemand = TblPurcDemand::where('demand_id',$id)->where('demand_type',$type)->first();
            }else{
                $purchaseDemand = new TblPurcDemand();
                $purchaseDemand->demand_id = ApiUtilities::uuid();
                $max_no = TblPurcDemand::where('demand_type',$type)->max('demand_no');
                $purchaseDemand->demand_no = $this->documentCode($max_no,'SDR');
            }
            $purchaseDemand->demand_type = $type;
            $purchaseDemand->supplier_id = $request->supplier_id;
            $purchaseDemand->demand_date = date('Y-m-d', strtotime($request->demand_date));
            $purchaseDemand->demand_forward_for_approval = 1;
            $purchaseDemand->demand_branch_to = $request->demand_branch_to;
            $purchaseDemand->demand_notes = $request->demand_notes;
            $purchaseDemand->demand_entry_status = 1;
            $purchaseDemand->business_id = Session::get('ApiDataSession')->business_id;
            $purchaseDemand->company_id = Session::get('ApiDataSession')->company_id;
            $purchaseDemand->branch_id = Session::get('ApiDataSession')->branch_id;
            $purchaseDemand->demand_user_id = Session::get('ApiDataSession')->user_id;
            $purchaseDemand->save();

            $del_DemandDtls = TblPurcDemandDtl::where('demand_id',$id)->get();
            foreach ($del_DemandDtls as $del_DemandDtl){
                TblPurcDemandDtl::where('demand_dtl_id',$del_DemandDtl->demand_dtl_id)->delete();
            }

            if(isset($request->pd)){
                foreach ($request->pd as $pd){
                    $DemandDtl = new TblPurcDemandDtl();
                    if(isset($id) && isset($pd['demand_dtl_id'])){
                        $DemandDtl->demand_id = $id;
                        $DemandDtl->demand_dtl_id = $pd['demand_dtl_id'];
                    }else{
                        $DemandDtl->demand_dtl_id = ApiUtilities::uuid();
                        $DemandDtl->demand_id  = $purchaseDemand->demand_id;
                    }
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
                    $DemandDtl->business_id = Session::get('ApiDataSession')->business_id;
                    $DemandDtl->company_id = Session::get('ApiDataSession')->company_id;
                    $DemandDtl->branch_id = Session::get('ApiDataSession')->branch_id;
                    $DemandDtl->demand_dtl_user_id = Session::get('ApiDataSession')->user_id;
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


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function destroy($id)
    {
        $type = 'stock_request';
        $data = [];
        DB::beginTransaction();
        try{

            $purchaseDemand = TblPurcDemand::where('demand_id',$id)->where('demand_type',$type)->first();
            $purchaseDemand->dtls()->delete();
            $purchaseDemand->delete();

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
