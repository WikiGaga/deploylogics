<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Models\TblPurcDemand;
use App\Models\TblPurcDemandDtl;
use App\Models\TblSoftBranch;
use App\Models\User;
use Illuminate\Http\Request;
// db and Validator
use App\Library\Utilities;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;


class StockRequestController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public static $page_title = 'Stock Request';
    public static $redirect_url = 'stock-request';
    public static $menu_dtl_id = '125';
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
        $data['form_type'] = 'stock_request';
        $data['page_data'] = [];
        $data['page_data']['title'] = self::$page_title;
        $data['page_data']['path_index'] = $this->prefixIndexPage.self::$redirect_url;;
        $data['page_data']['create'] = '/'.self::$redirect_url.$this->prefixCreatePage;
        if(isset($id)){
            if(TblPurcDemand::where('demand_id','LIKE',$id)->exists()){
                $data['page_data'] = array_merge($data['page_data'], Utilities::editForm());
                $data['permission'] = self::$menu_dtl_id.'-edit';
                $data['id'] = $id;
                $data['current'] = TblPurcDemand::with('dtls','supplier')->where('demand_id',$id)->where('demand_type',$type)->first();
                $data['page_data']['print'] = '/'.self::$redirect_url.'/print/'.$id;
                $data['document_code'] = $data['current']->demand_no;
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
                'code_prefix'       => strtoupper('SDR'),
                'code_type_field'   => 'demand_type',
                'code_type'         => $type,
            ];
            $data['document_code'] = Utilities::documentCode($doc_data);
        }
        $data['branch'] = TblSoftBranch::get();
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
        return view('inventory.stock_request.form',compact('data'));
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
            'demand_branch_to' => 'required|numeric',
        ]);
        if ($validator->fails()) {
            $data['validator_errors'] = $validator->errors();
            return $this->jsonErrorResponse($data, trans('message.required_fields'), 422);
        }
        DB::beginTransaction();
        try{
            if(isset($id)){
                $purchaseDemand = TblPurcDemand::where('demand_id',$id)->where('demand_type',$type)->first();
            }else{
                $purchaseDemand = new TblPurcDemand();
                $purchaseDemand->demand_id = Utilities::uuid();
                $doc_data = [
                    'biz_type'          => 'branch',
                    'model'             => 'TblPurcDemand',
                    'code_field'        => 'demand_no',
                    'code_prefix'       => strtoupper('SDR'),
                    'code_type_field'   => 'demand_type',
                    'code_type'         => $type,
                ];
                $document_code = Utilities::documentCode($doc_data);
                $purchaseDemand->demand_no = $document_code;
            }
            $form_id = $purchaseDemand->demand_id;
            $purchaseDemand->demand_type = $type;
            $purchaseDemand->demand_date = date('Y-m-d', strtotime($request->demand_date));
            $purchaseDemand->demand_forward_for_approval = 1;
            $purchaseDemand->demand_branch_to = $request->demand_branch_to;
            $purchaseDemand->demand_notes = $request->demand_notes;
            $purchaseDemand->demand_entry_status = 1;
            $purchaseDemand->business_id = auth()->user()->business_id;
            $purchaseDemand->company_id = auth()->user()->company_id;
            $purchaseDemand->branch_id = auth()->user()->branch_id;
            $purchaseDemand->demand_user_id = auth()->user()->id;
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
                        $DemandDtl->demand_dtl_id = Utilities::uuid();
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
        $data['type'] = 'stock_request';
        $data['title'] = 'Stock Request';
        $data['permission'] = self::$menu_dtl_id.'-print';
        if(isset($id)){
            if(TblPurcDemand::where('demand_id','LIKE',$id)->where('demand_type',$data['type'])->where(Utilities::currentBC())->exists()){
                $data['current'] = TblPurcDemand::with('dtls')
                    ->where('demand_id',$id)->where('demand_type',$data['type'])->where(Utilities::currentBC())->first();
                $data['branch_to'] = TblSoftBranch::where('branch_id',$data['current']->demand_branch_to)->first();
                $data['branch_from'] = TblSoftBranch::where('branch_id',$data['current']->branch_id)->first();
            }else{
                abort('404');
            }
        }
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
        $type = 'stock_request';
        $data = [];
        DB::beginTransaction();
        try{

            $purchaseDemand = TblPurcDemand::where('demand_id',$id)->where('demand_type',$type)->first();
            $purchaseDemand->dtls()->delete();
            $purchaseDemand->delete();

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
