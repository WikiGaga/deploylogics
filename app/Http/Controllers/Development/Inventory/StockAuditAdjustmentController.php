<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Library\Utilities;
use App\Models\TblDefiStore;
use App\Models\TblSoftBranch;
use App\Models\TblPurcGroupItem;
use App\Models\TblInveStock;
use App\Models\TblInveStockDtl;
use App\Models\ViewInveDisplayLocation;
use App\Models\TblPurcBrand;
use App\Models\ViewInveStock;


use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Importer;
use Illuminate\Validation\ValidationException;

class StockAuditAdjustmentController extends Controller
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

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function create($id = null)
    {
        $data = [];
        $data['stock_code_type'] = 'sa';
        $data['stock_menu_id'] = '309';
        $data['page_data']['title'] = 'Stock Audit';
        //$data['page_data']['path_index'] = $this->prefixIndexPage.'stock-audit-adjustment/stock-audit';
        $data['permission'] = $data['stock_menu_id'].'-create';
        $data['page_data'] = array_merge($data['page_data'], Utilities::newForm());
        $data['branch'] = TblSoftBranch::where(Utilities::currentBC())->get();
        $data['group_item'] = TblPurcGroupItem::with('last_level')->where('group_item_level',2)->where(Utilities::currentBC())->orderByRaw(DB::raw('lower(group_item_name)'))->get();
        $data['brand'] = TblPurcBrand::where('brand_entry_status',1)->where(Utilities::currentBC())->get();

       // dd($data);

        return view('inventory.stock_audit.form', compact('data'));
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
        $errData = [];
        $valid = [
            'start_date' => 'required|date_format:d-m-Y',
        ];
        //dd($valid);
        $validator = Validator::make($request->all(), $valid);
        if ($validator->fails()) {
            $data['validator_errors'] = $validator->errors();
            return $this->jsonErrorResponse($data, trans('message.required_fields'), 422);
        }
        if(!isset($id)){
            if(!isset($request->check_id)){
                return $this->jsonErrorResponse($data, 'at least One Product is required');
            }
        }

        DB::beginTransaction();
        try{
            $formType ='sa';
            if(isset($id)){
                $stock = TblInveStock::where('stock_id',$id)->first();
                $store_to = isset($request->store_id)?$request->store_id:"";
                $stock->stock_store_to_id =  isset($store_to)?$store_to:"";
            }else{
                $stock = new TblInveStock();
                $stock->stock_id =  Utilities::uuid();
                $doc_data = [
                    'biz_type'          => 'branch',
                    'model'             => 'TblInveStock',
                    'code_field'        => 'stock_code',
                    'code_prefix'       => strtoupper($formType),
                    'code_type_field'   => 'stock_code_type',
                    'code_type'         => $formType
    
                ];
                $stock->stock_code =  Utilities::documentCode($doc_data);
                $stock->audit_name = $request->audit_name;
            }


            $form_id = $stock->stock_id;
            $stock->stock_code_type =  $formType;
            $stock->stock_date =   date('Y-m-d', strtotime($request->start_date));
            $stock->stock_menu_id =  '309';
            $stock->stock_remarks = $request->remarks;
            $stock->stock_entry_status = 1;
            
            if(isset($id)){
                $stock->stock_store_from_id =  isset($request->store)?$request->store:"";
            }
            $stock->business_id = auth()->user()->business_id;
            $stock->company_id = auth()->user()->company_id;
            $stock->branch_id = auth()->user()->branch_id;
            $stock->stock_user_id = auth()->user()->id;
            $stock->save();

            $del_Dtls = TblInveStockDtl::where('stock_id',$id)->get();
            foreach ($del_Dtls as $del_Dtl){
                TblInveStockDtl::where('stock_dtl_id',$del_Dtl->stock_dtl_id)->delete();
            }

            if(isset($request->pd))
            {
                $keyy = 1;
                foreach($request->pd as $dt)
                {
                    $ddtl = new TblInveStockDtl();
                    $ddtl->stock_id =  $stock->stock_id;
                    $ddtl->stock_dtl_id =  Utilities::uuid();
                    $ddtl->stock_dtl_sr_no =  $keyy++;
                    $ddtl->product_id =  $dt['product_id'];
                    $ddtl->product_barcode_id =  $dt['product_barcode_id'];
                    $ddtl->product_barcode_barcode =  $dt['pd_barcode'];
                    $ddtl->uom_id =  $dt['uom_id'];
                    $ddtl->stock_dtl_packing = isset($dt['product_barcode_packing'])?$dt['product_barcode_packing']:'';
                    $ddtl->cost_rate = isset($dt['cost_rate'])?$dt['cost_rate']:'';
                    $ddtl->cost_amount = isset($dt['cost_amount'])?$dt['cost_amount']:'';
                    $ddtl->stock_dtl_stock_quantity =  isset($dt['stock_quantity'])?$this->addNo($dt['stock_quantity']):"";

                    if(isset($dt['physical_quantity']))
                    {
                        $ddtl->stock_dtl_physical_quantity =  isset($dt['physical_quantity'])?$this->addNo($dt['physical_quantity']):"";
                        $adjustmentQty = (float)$dt['physical_quantity'] - (float)$dt['stock_quantity'];
                        $ddtl->stock_dtl_quantity = $adjustmentQty; // adjustment qty
                        $ddtl->stock_dtl_qty_base_unit = $adjustmentQty;
                    }

                    $ddtl->business_id = auth()->user()->business_id;
                    $ddtl->company_id = auth()->user()->company_id;
                    $ddtl->branch_id = auth()->user()->branch_id;
                    $ddtl->dtl_user_id = auth()->user()->id;
                    $ddtl->save();
                }
            }

            if(isset($request->check_id))
            {
                $key = 1;
                foreach($request->check_id as $pd)
                {
                    $array = explode('@',$pd);

                    $product_id = isset($array[0])?$array[0]:"";
                    $product_barcode_id = isset($array[1])?$array[1]:"";
                    $product_barcode_barcode = isset($array[2])?$array[2]:"";
                    $uom_id = isset($array[3])?$array[3]:"";
                    $product_barcode_packing = isset($array[4])?$array[4]:"";
                    $stock_quantity = isset($array[5])?$array[5]:"";
                    $cost_rate = isset($array[6])?$array[6]:"";

                    $dtl = new TblInveStockDtl();
                    $dtl->stock_id =  $stock->stock_id;
                    $dtl->stock_dtl_id =  Utilities::uuid();
                    $dtl->stock_dtl_sr_no =  $key++;
                    $dtl->product_id =  $product_id;
                    $dtl->product_barcode_id =  $product_barcode_id;
                    $dtl->product_barcode_barcode =  $product_barcode_barcode;
                    $dtl->uom_id =  $uom_id;
                    $dtl->cost_rate =  $cost_rate;
                    $dtl->stock_dtl_packing = $product_barcode_packing;
                    $dtl->stock_dtl_stock_quantity =  isset($stock_quantity)?$this->addNo($stock_quantity):"";
                    
                    $dtl->business_id = auth()->user()->business_id;
                    $dtl->company_id = auth()->user()->company_id;
                    $dtl->branch_id = auth()->user()->branch_id;
                    $dtl->dtl_user_id = auth()->user()->id;
                    $dtl->save();
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
            return $this->jsonErrorResponse($errData, $e->getMessage(), 200);
        }
        DB::commit();
        if(isset($id)){
            $data = array_merge($data, Utilities::returnJsonEditForm());
            $data['redirect'] = $this->prefixIndexPage.'deal-setup';
            return $this->jsonSuccessResponse($data, trans('message.update'), 200);
        }else{
            $data = array_merge($data, Utilities::returnJsonNewForm());
            $data['redirect'] = '/'.'deal-setup'.$this->prefixCreatePage.'/'.$form_id;
            return $this->jsonSuccessResponse($data, trans('message.create'), 200);
        }
    }

    public function print(Request $request,$id)
    {
        $data['title'] = 'Audit Stock Adjustment';
        $data['stock_code_type'] = 'sa';
        $data['stock_menu_id'] = 312;
        $data['type'] = $request->print;

        if(isset($id)){
            if(TblInveStock::where('stock_id','LIKE',$id)->exists()){
                $data['current'] = TblInveStock::with('audit_stock_dtls','product','barcode')->where('stock_id',$id)->where('stock_code_type',$data['stock_code_type'])->first();
                $data['current_dtl'] = ViewInveStock::where(Utilities::currentBCB())->where('stock_id',$id)->where('stock_code_type',$data['stock_code_type'])->orderBy('product_name')->get();
                $data['permission'] = $data['stock_menu_id'].'-print';
            }else{
                abort('404');
            }
        }
        $data['store_from'] = TblDefiStore::where('store_id',$data['current']->stock_store_from_id)->first();
        $data['store_to'] = TblDefiStore::where('store_id',$data['current']->stock_store_to_id)->first();
        $data['branch_from'] = TblSoftBranch::where('branch_id',$data['current']->stock_branch_from_id)->first();
        $data['branch_to'] = TblSoftBranch::where('branch_id',$data['current']->stock_branch_to_id)->first();
        $data['display_location'] = ViewInveDisplayLocation::where('branch_id',auth()->user()->branch_id)->where('store_id',$data['current']->stock_store_from_id)->first();
        $data['rate_by'] = config('constants.rate_by');

        return view('prints.inventory.stock_adjustment.audit_stock_adjustment', compact('data'));
    }





    public function adjustmentTag(Request $request){
        $data['page_data']['title'] = 'Stock Audit Adjustment';
        $formUrl = 'stock_audit';
        $data['stock_code_type'] = 'sa';
        $data['stock_menu_id'] = '312';
        $data['form_type'] = 'stock-audit';
        
        return response()->json(['status'=>'success']);
    }

    public function AuditCloseTag(Request $request){
        $data = [];
      
        if(TblInveStock::where('stock_id','LIKE',$request->data[0])->exists())
        {
            $row = TblInveStock::where('stock_id',$request->data[0])->where('stock_code_type','sa')->first();
            $row->audit_close = 1;
            $row->audit_un_post = 0;
            $row->update();

            return $this->jsonSuccessResponse($data, trans('Audit Successfully Close'), 200);
        }else{
            abort('404');
        }
        
        return response()->json(['status'=>'success']);
    }

    public function AuditSuspendTag(Request $request){
        $data = [];
      
        if(TblInveStock::where('stock_id','LIKE',$request->data[0])->exists())
        {
            $row = TblInveStock::where('stock_id',$request->data[0])->where('stock_code_type','sa')->first();
            
            if($row->audit_complete != "1")
            {
                $row->audit_suspend = 1;
                $row->update();
    
                return response()->json(['status'=>'success']);
            }else{
                return response()->json(['status'=>'error']);
            }
        }else{
            abort('404');
        }
        
    }

    public function AuditCompleteTag(Request $request){
        $data = [];
      
        if(TblInveStock::where('stock_id','LIKE',$request->data[0])->exists())
        {
            $row = TblInveStock::where('stock_id',$request->data[0])->where('stock_code_type','sa')->first();
            $row->audit_complete = 1;
            $row->audit_un_post = 0;
            $row->update();

            return $this->jsonSuccessResponse($data, trans('Audit Successfully Completed'), 200);
        }else{
            abort('404');
        }
        
        return response()->json(['status'=>'success']);
    }
    
    public function AuditUnPostTag(Request $request){
        $data = [];
      
        if(TblInveStock::where('stock_id','LIKE',$request->data[0])->exists())
        {
            $row = TblInveStock::where('stock_id',$request->data[0])->where('stock_code_type','sa')->first();
            
            if($row->audit_complete == "1")
            {
                $row->audit_close = 0;
                $row->audit_suspend = 0;
                $row->audit_complete = 0;
                $row->audit_un_post = 1;
                $row->update();
                return $this->jsonSuccessResponse($data, trans('Audit Successfully Un-Posted.'), 200);
            }

        }else{
            abort('404');
        }
        
        return response()->json(['status'=>'success']);
    }

    public function productListProductItemTax(Request $request)
    {
        $data = [];

        /*if(empty($request->supplier_id) && empty($request->group_item_id)){
            return $this->jsonErrorResponse($data, 'at least One is required between Group Item Or Supplier');
        }*/
        if(empty($request->supplier_id)){
            if(empty($request->group_item_id)){
                return $this->jsonErrorResponse($data, 'at least One is required Group Item');
            }
        }



        if(empty($request->branch_id) && count($request->branch_id) == 0){
            return $this->jsonErrorResponse($data, 'Branch is required');
        }
        DB::beginTransaction();
        try{
            $qty_where = "";
            $where = "";

            if(!empty($request->qty_action)){
                if($request->qty_action == '>')
                {
                    $qty_where = " having SUM(GAGA.PRODUCT_QTY) > ".$request->qty_filter;
                }
                if($request->qty_action == '<')
                {
                    $qty_where = " having SUM(GAGA.PRODUCT_QTY) < ".$request->qty_filter;
                }
                if($request->qty_action == '=')
                {
                    $qty_where = " having SUM(GAGA.PRODUCT_QTY) = ".$request->qty_filter;
                }
                if($request->qty_action == '<>')
                {
                    $qty_where = " having SUM(GAGA.PRODUCT_QTY) <> ".$request->qty_filter;
                }
            }

            if(!empty($request->supplier_id)){
                $where .= " and PR.supplier_id = ".$request->supplier_id;
            }

            if(!empty($request->group_item_id)){
                $where .= " and PR.group_item_id = ".$request->group_item_id;
            }
           
            /*
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
            */

            /*
            $qry = "SELECT DISTINCT 
                PR.product_id,
                MAX (PR.product_name)                product_name,
                MAX (PR.UOM_ID)                      UOM_ID,
                MAX (PR.uom_name)                    uom_name,
                MAX (PR.product_barcode_packing)     product_barcode_packing,
                MAX (PR.product_barcode_barcode)     product_barcode_barcode,
                MAX (PR.GROUP_ITEM_NAME)             GROUP_ITEM_NAME,
                MAX (PR.GROUP_ITEM_PARENT_NAME)      GROUP_ITEM_PARENT_NAME,
                MAX (PR.PRODUCT_BARCODE_ID)          PRODUCT_BARCODE_ID,
                NVL (max (GAGA.COST_RATE), 0)      COST_RATE,
                NVL (max (GAGA.PRODUCT_QTY), 0)      stock
            FROM (SELECT product_id,
                    BRANCH_ID,
                    max(PRODUCT_BARCODE_ID) PRODUCT_BARCODE_ID ,
                    max(COST_RATE)     AS COST_RATE,
                    sum(QTY_BASE_UNIT_VALUE)     AS PRODUCT_QTY
                FROM 
                    VW_PURC_STOCK_DTL
                WHERE branch_id = ".auth()->user()->branch_id."
                group by product_id ,  BRANCH_ID 
            ) GAGA
            INNER JOIN VW_PURC_PRODUCT_BARCODE PR
            ON GAGA.PRODUCT_ID = PR.PRODUCT_ID
            WHERE  (created_at <= to_date ('".$request->start_date."', 'dd/mm/yyyy')) 
                $where 
            GROUP BY PR.product_id
            $qty_where
            ORDER BY product_name";
            */

        

            $qry = "SELECT DISTINCT 
                PR.product_id,
                MAX (PR.product_name)                product_name,
                MAX (PR.UOM_ID)                      UOM_ID,
                MAX (PR.uom_name)                    uom_name,
                MAX (PR.product_barcode_packing)     product_barcode_packing,
                PROD_BARCODE.product_barcode_barcode     product_barcode_barcode,
                MAX (PR.GROUP_ITEM_NAME)             GROUP_ITEM_NAME,
                MAX (PR.GROUP_ITEM_PARENT_NAME)      GROUP_ITEM_PARENT_NAME,
                MAX (PR.PRODUCT_BARCODE_ID)          PRODUCT_BARCODE_ID,
                NVL (max (GAGA.COST_RATE), 0)      COST_RATE,
                NVL (max (GAGA.PRODUCT_QTY), 0)      stock
            FROM (SELECT product_id,
                    BRANCH_ID,
                    max(PRODUCT_BARCODE_ID) PRODUCT_BARCODE_ID ,
                    max(COST_RATE)     AS COST_RATE,
                   -- sum(QTY_BASE_UNIT_VALUE)     AS PRODUCT_QTY
                   (SUM (NVL (QTY_IN, 0)) - SUM (NVL (QTY_OUT, 0)) ) PRODUCT_QTY
                FROM 
                    VW_PURC_STOCK_DTL
                WHERE branch_id = ".auth()->user()->branch_id."
                group by product_id ,  BRANCH_ID 
            ) GAGA
            INNER JOIN VW_PURC_PRODUCT_BARCODE PR
            ON GAGA.PRODUCT_ID = PR.PRODUCT_ID
            LEFT OUTER JOIN
         (  SELECT MAX (PRODUCT_BARCODE_BARCODE)     PRODUCT_BARCODE_BARCODE,
                   PRODUCT_ID
              FROM tbl_purc_product_barcode
             WHERE BASE_BARCODE = 1
          GROUP BY PRODUCT_ID) PROD_BARCODE
             ON GAGA.PRODUCT_ID = PROD_BARCODE.PRODUCT_ID
            WHERE  (created_at <= to_date ('".$request->start_date."', 'dd/mm/yyyy')) 
                $where 
            GROUP BY PR.product_id, PROD_BARCODE.product_barcode_barcode
            $qty_where
            ORDER BY product_name";
            

       // dd($qry);
        
            $data['products'] = DB::select($qry);

        }catch (Exception $e){
            DB::rollback();
            return $this->jsonErrorResponse($data, $e->getMessage(), 200);
        }
        DB::commit();

        return $this->jsonSuccessResponse($data, 'Data loaded successfully', 200);

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

    public function destroy($id)
    {
        $data = [];
        DB::beginTransaction();
        try{
            $stock= TblInveStock::where('stock_id',$id)->first();
            $audit_complete = $stock->audit_complete;

            if($audit_complete == '0')
            {
                $stock->stock_dtls()->delete();
                $stock->delete();
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
        
        if($audit_complete == '0')
        {
            return $this->jsonSuccessResponse($data, trans('message.delete'), 200);
        }
        if($audit_complete == '1')
        {
            return $this->jsonErrorResponse($data, trans('This Audit is completed.'), 200);
        }
    }
}
