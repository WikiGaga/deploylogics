<?php

namespace App\Http\Controllers\Purchase;

use App\Http\Controllers\Controller;
use App\Library\Utilities;
use App\Models\TblPurcDemand;
use App\Models\TblPurcDemandApproval;
use App\Models\TblPurcDemandApprovalDtl;
use App\Models\TblPurcDemandDtl;
use App\Models\TblPurcLpoDtl;
use App\Models\TblPurcProductBarcodePurchRate;
use App\Models\User;
use Illuminate\Http\Request;

// db and Validator
use Illuminate\Validation\Rule;
use Validator;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class PurchaseDemandApproveController extends Controller
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
    public static $page_title = 'Demand Approval';
    public static $redirect_url = 'demand-approve';
    public static $menu_dtl_id = '20';
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($id = null)
    {
        $data['page_data'] = [];
        $data['page_data']['title'] = self::$page_title;
        $data['page_data']['path_index'] = $this->prefixIndexPage.self::$redirect_url;;
        $data['page_data']['create'] = '/'.self::$redirect_url.$this->prefixCreatePage;
        if(isset($id)){
            $data['page_data'] = array_merge($data['page_data'], Utilities::editForm());
            $data['id'] = $id;
            $data['current'] = TblPurcDemandApprovalDtl::with('product','branch','uom','barcode','demand')->where('demand_approval_dtl_id',$id)->orderBy('sr_no', 'ASC')->get();
            $data['code_date'] = TblPurcDemandApprovalDtl::select(['demand_approval_dtl_code','demand_approval_dtl_date'])->where('demand_approval_dtl_id',$id)->first();
            $data['page_data']['print'] = '/'.self::$redirect_url.'/print/'.$id;
            $data['demand'] = TblPurcDemandDtl::with('product','uom','packing','branch')
                ->where('demand_id',$id)
                ->where('demand_dtl_approve_status','pending')
                ->where('demand_dtl_entry_status',1)
                ->get();
            $demandQry = "select dem.DEMAND_ID,dem.DEMAND_NO,dem.DEMAND_DATE,dem.name,dem.DEMAND_NOTES,dem.branch_name from (
                                select d.DEMAND_ID,d.DEMAND_NO,d.DEMAND_DATE,u.name,d.DEMAND_NOTES,b.branch_name,dap.sr_no
                                from TBL_PURC_DEMAND_APPROVAL_DTL dap
                                join TBL_PURC_DEMAND d on  d.DEMAND_ID = dap.DEMAND_ID
                                join Users u on u.id = d.SALESMAN_ID
                                join TBL_SOFT_BRANCH b on  b.branch_id = d.branch_id
                                where DEMAND_APPROVAL_DTL_ID = $id
                                group by (d.DEMAND_ID,d.DEMAND_NO,d.DEMAND_DATE,u.name,d.DEMAND_NOTES,b.branch_name,dap.sr_no)
                                order by dap.sr_no asc
                            ) dem
                            group by (dem.DEMAND_ID,dem.DEMAND_NO,dem.DEMAND_DATE,dem.name,dem.DEMAND_NOTES,dem.branch_name)";

            $data['demand_list'] = DB::select($demandQry);
            //dd($data['demand_list']);
        }else{
            $data['page_data'] = array_merge($data['page_data'], Utilities::newForm());
            $data['document_code'] = $this->documentCode(TblPurcDemandApprovalDtl::max('demand_approval_dtl_code'),'DA');
            
            $data['demand'] = DB::select("select s.supplier_name,demand.DEMAND_ID,demand.DEMAND_NO,u.name ,demand.demand_date,demand.demand_notes,dtl.demand_dtl_approve_status,b.branch_name 
            from tbl_purc_demand demand
            join tbl_purc_demand_dtl dtl on demand.DEMAND_ID = dtl.DEMAND_ID
            join tbl_soft_branch b on b.branch_id = demand.branch_id
            join users u on u.id = demand.salesman_id
            left join tbl_purc_supplier s on s.supplier_id = demand.supplier_id
            where demand.DEMAND_FORWARD_FOR_APPROVAL = 1 and demand.demand_type = 'purchase_demand'
            and dtl.demand_dtl_approve_status = 'pending' and demand.branch_id = ". auth()->user()->branch_id ."
            group by (s.supplier_name,demand.DEMAND_ID,demand.DEMAND_NO,demand.demand_date,demand.demand_notes,dtl.demand_dtl_approve_status,b.branch_name,u.name,demand.created_at) 
            order by demand.created_at desc");
        }
        return view('purchase.demand_approve.form', compact('data'));
    }

    public function DemandDetails($id)
    {
        $data = TblPurcDemandDtl::
            with(['product','barcode','uom','packing','branch'])
            ->where('demand_id',$id)
            ->where('demand_dtl_approve_status','pending')
            ->where('demand_dtl_entry_status',1)
            ->orderBy('sr_no','asc')
            ->get();
        // Get Purcahse Rate From Database
        foreach ($data as $product) {
            $purchaseRate = TblPurcProductBarcodePurchRate::select('product_barcode_purchase_rate')
            ->where('product_barcode_id',$product->product_barcode_id)
            ->where('branch_id' , $product->branch_id)
            ->first();
            $product['purchase_rate'] = number_format($purchaseRate->product_barcode_purchase_rate ?? 0 , 3);
        }
        return response()->json($data,200);
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
        $validator = Validator::make($request->all(), []);
        if ($validator->fails()) {
            $data['validator_errors'] = $validator->errors();
            return $this->jsonErrorResponse($data, trans('message.required_fields'), 422);
        }
        DB::beginTransaction();
        try{
            if(isset($id)){
                $new_id = $id;
                $documentCode = TblPurcDemandApprovalDtl::where('demand_approval_dtl_id' , $id)->first('demand_approval_dtl_code');
                $documentCode = $documentCode->demand_approval_dtl_code;
                TblPurcDemandApprovalDtl::where('demand_approval_dtl_id' , $id)->delete();
            }else{
                $new_id = Utilities::uuid();
                $documentCode = $this->documentCode(TblPurcDemandApprovalDtl::max('demand_approval_dtl_code'),'DA');
            }
            $form_id = $new_id;
            $demand_approve_date = $request->demand_approve_date;
            $demand_notes = $request->demand_notes;
            if($request->pd){
                $sr_no = 1;
                foreach($request->pd as $pd){
                    if($pd['action']=='approved' || $pd['action']=='reject'){
                        $demandApproveDtl = new TblPurcDemandApprovalDtl();
                        $demandApproveDtl->demand_dtl_id = $pd['demand_dtl_id'];
                        $demandApproveDtl->demand_id = $pd['demand_id'];
                        $demandApproveDtl->demand_approval_dtl_id = $new_id;
                        $demandApproveDtl->demand_approval_dtl_code = $documentCode;
                        $demandApproveDtl->sr_no = $sr_no++;
                        $demandApproveDtl->demand_approval_dtl_date = date('Y-m-d', strtotime($demand_approve_date));
                        $demandApproveDtl->demand_approval_dtl_physical_stock = $pd['physical_stock'];
                        $demandApproveDtl->demand_approval_dtl_store_stock = $pd['store_stock'];
                        $demandApproveDtl->demand_approval_dtl_stock_match = $pd['stock_match'];
                        $demandApproveDtl->demand_approval_dtl_suggest_quantity1 = $pd['suggest_qty_1'];
                        $demandApproveDtl->demand_approval_dtl_suggest_quantity2 = $pd['suggest_qty_2'];
                        $demandApproveDtl->demand_approval_dtl_demand_quantity = '';
                        $demandApproveDtl->demand_approval_dtl_wip_lpo_stock = $pd['wiplpo_stock'];
                        $demandApproveDtl->demand_approval_dtl_pur_ret_in_waiting = $pd['pur_ret'];
                        $demandApproveDtl->product_barcode_purchase_rate = $pd['purchase_rate'] ?? 0;
                        $demandApproveDtl->demand_approval_dtl_demand_qty = $pd['demand_qty'];
                        $demandApproveDtl->demand_approval_dtl_approve_qty = $pd['approve_qty'];
                        $demandApproveDtl->demand_approval_dtl_notes = $pd['remarks'];
                        $demandApproveDtl->demand_approval_dtl_entry_notes = $demand_notes;
                        $demandApproveDtl->demand_approval_dtl_pending_status = "";
                        $demandApproveDtl->demand_approval_dtl_approve_status = $pd['action'];
                        $demandApproveDtl->demand_approval_dtl_reject_status = "";
                        $demandApproveDtl->demand_approval_dtl_entry_status = 1;
                        if(isset($pd['notes_id']) && $pd['notes_id'] != ''){
                            $demandApproveDtl->demand_approval_dtl_remarks_id = $pd['notes_id'];
                            $demandApproveDtl->demand_approval_dtl_remarks = $pd['notes'];
                        }
                        $demandApproveDtl->product_barcode_barcode = $pd['bar_code'];
                        $demandApproveDtl->product_barcode_id = $pd['product_barcode_id'];
                        $demandApproveDtl->demand_approval_dtl_packing = $pd['packing_name'];
                        $demandApproveDtl->product_id = $pd['product_id'];
                        $demandApproveDtl->uom_id = $pd['uom_id'];
                        $demandApproveDtl->branch_id = $pd['branch_id'];
                        $demandApproveDtl->demand_approval_dtl_user_id = auth()->user()->id;
                        $demandApproveDtl->demand_approval_dtl_branch_id = auth()->user()->branch_id;
                        $demandApproveDtl->business_id = auth()->user()->business_id;
                        $demandApproveDtl->company_id = auth()->user()->company_id;
                        $demandApproveDtl->save();

                        $demandDtl = TblPurcDemandDtl::where('demand_dtl_id',$pd['demand_dtl_id'])->first();
                        $demandDtl->demand_dtl_approve_status = $pd['action'];
                        $demandDtl->save();
                    }
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
        if(isset($id)){
            $data = array_merge($data, Utilities::returnJsonEditForm());
            $data['redirect'] = '/'.self::$redirect_url.$this->prefixCreatePage.'/'.$form_id;
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
        $data['title'] = 'Purchase Demand Approval';
        $data['permission'] = self::$menu_dtl_id.'-print';
        if(isset($id)){
                $data['current'] = TblPurcDemandApprovalDtl::with('product','branch','demand')->where('demand_approval_dtl_id',$id)->orderBy('sr_no', 'ASC')->get();
                $data['code_date'] = TblPurcDemandApprovalDtl::select(['demand_approval_dtl_code','demand_approval_dtl_date','demand_approval_dtl_entry_notes'])->where('demand_approval_dtl_id',$id)->first();
            }else{
                abort('404');
            }
        return view('prints.demand_approval_print',compact('data'));
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
            $dataExist = TblPurcLpoDtl::where('demand_approval_dtl_id','Like',$id)->exists();
            if($dataExist === false)
            {
                $del_ApprovalDtls = TblPurcDemandApprovalDtl::where('demand_approval_dtl_id',$id)->get();
                foreach ($del_ApprovalDtls as $Demand_Approval){
                    TblPurcDemandApprovalDtl::where('demand_approval_dtl_id',$Demand_Approval->demand_approval_dtl_id)->delete();
                }
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
