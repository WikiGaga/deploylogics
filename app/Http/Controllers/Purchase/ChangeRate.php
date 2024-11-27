<?php

namespace App\Http\Controllers\Purchase;

use App\Models\TblInveStock;
use App\Models\TblInveStockDtl;
use App\Models\TblPurcChangeRateBranches;
use App\Models\TblPurcProductBarcodePurchRate;
use App\Models\TblSoftBranch;
use Exception;
use Validator;
use App\Library\Utilities;
use App\Models\TblPurcGrn;
use Illuminate\Http\Request;
use App\Models\TblPurcProduct;
use Illuminate\Validation\Rule;
use App\Models\TblPurcChangeRate;
use Illuminate\Support\Facades\DB;
use App\Models\TblPurcRateCategory;
use App\Http\Controllers\Controller;

// db and Validator
use App\Models\TblPurcChangeRateDtl;
use App\Models\TblPurcProductBarcode;
use App\Models\ViewStock;
use App\Models\TblSoftUserActivityLog;
use Illuminate\Database\QueryException;
use App\Models\ViewPurcProductBarcodeFirst;
use App\Models\TblPurcProductBarcodeSaleRate;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ChangeRate extends Controller
{
    public static $page_title = 'Update Product Price';
    public static $redirect_url = 'change-rate';
    public static $menu_dtl_id = '135';
    //public static $menu_dtl_id = '143';
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
        $data['page_data'] = [];
        $data['form_type'] = 'change_rate';
        $data['page_data']['title'] = self::$page_title;
        $data['page_data']['path_index'] = $this->prefixIndexPage.self::$redirect_url;
        $data['page_data']['create'] = '/'.self::$redirect_url.$this->prefixCreatePage;
        if(isset($id)){
            if(TblPurcChangeRate::where('change_rate_id','LIKE',$id)->where(Utilities::currentBCB())->exists()){
               // $data['permission'] = self::$menu_dtl_id.'-edit';
                $data['permission'] = '-edit';
                $data['page_data'] = array_merge($data['page_data'], Utilities::editForm());
                $data['id'] = $id;
                $data['current'] = TblPurcChangeRate::with('change_rate_dtl')->where('change_rate_id',$id)->where(Utilities::currentBCB())->first();
                $data['customer_code'] = $data['current']->change_rate_code;
                $data['page_data']['print'] = '/'.self::$redirect_url.'/print/'.$id;
            }else{
                abort('404');
            }
        }else{
            $data['permission'] = self::$menu_dtl_id.'-create';
            $data['page_data'] = array_merge($data['page_data'], Utilities::newForm());
            $doc_data = [
                'biz_type'          => 'branch',
                'model'             => 'TblPurcChangeRate',
                'code_field'        => 'change_rate_code',
                'code_prefix'       => strtoupper('chrate')
            ];
            $data['customer_code'] = Utilities::documentCode($doc_data);
        }
        $data['categories'] = TblPurcRateCategory::where('rate_category_entry_status',1)->where(Utilities::currentBC())->orderBy('rate_category_id')->get();
        /*$arr = [
            'biz_type' => 'branch',
            'code' => $data['customer_code'],
            'link' => $data['page_data']['create'],
            'table_name' => 'tbl_purc_change_rate',
            'col_id' => 'change_rate_id',
            'col_code' => 'change_rate_code',
        ];
        $data['switch_entry'] = $this->switchEntry($arr);*/
        $data['branch'] = TblSoftBranch::where(Utilities::currentBC())->get();

        return view('purchase.change_rate.form',compact('data'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $id = null)
    {
        // dd($request->toArray());
        $data = [];
        $validator = Validator::make($request->all(), [
            'change_rate_name' => 'required|max:100',
            'branch_ids' => 'required|not_in:0|array',
            /*'change_rate_category' => 'required',*/
            'pd.*.product_id' => 'required|numeric',
            'pd.*.product_barcode_id' => 'required|numeric',
        ]);
        if ($validator->fails()) {
            $data['validator_errors'] = $validator->errors();
            return $this->jsonErrorResponse($data, trans('message.required_fields'), 200);
        }
        if(empty($request->pd)){
            return $this->returnjsonerror("Please Enter Product Detail",200);
        }
        if(isset($id)){ // change rate will not update => 'client requirement'
            return $this->returnjsonerror("Not Allow to Update this entry",200);
        }
       // dd($request->pd);
        foreach($request->pd as $pd)
        {
            if($pd['new_sale_rate']  < $pd['new_tp'])
            {
                return $this->returnjsonerror("Sale Rate is less than New TP",200);
            }
        }

        DB::beginTransaction();
        try{
            if(isset($id)){
                $changeRate = TblPurcChangeRate::where('change_rate_id',$id)->where(Utilities::currentBCB())->first();
            }else{
                $changeRate = new TblPurcChangeRate();
                $changeRate->change_rate_id = Utilities::uuid();
                $doc_data = [
                    'biz_type'          => 'branch',
                    'model'             => 'TblPurcChangeRate',
                    'code_field'        => 'change_rate_code',
                    'code_prefix'       => strtoupper('chrate')
                ];
                $changeRate->change_rate_code = Utilities::documentCode($doc_data);
                $new = true;
            }

            $form_id = $changeRate->change_rate_id;
            $changeRate->change_rate_name = $request->change_rate_name;
            $changeRate->supplier_id = $request->supplier_id;
            /*$changeRate->change_rate_category = $request->change_rate_category;*/
            $changeRate->change_rate_date = date('Y-m-d', strtotime($request->change_rate_date));;
            $changeRate->change_rate_notes = $request->change_rate_notes;
            $changeRate->ref_grn_id = $request->ref_grn_id ?? '';
            $changeRate->ref_grn_code = $request->ref_grn_code ?? '';
            $changeRate->business_id = auth()->user()->business_id;
            $changeRate->company_id = auth()->user()->company_id;
            $changeRate->branch_id = auth()->user()->branch_id;
            $changeRate->change_rate_user_id = auth()->user()->id;
            $changeRate->save();
            if(isset($request->pd))
            {
                foreach($request->pd as $pd)
                {
                    $get_branch_central_rate = TblSoftBranch::where('branch_id',$request->branch_ids)->first();
                    $get_warranty = TblPurcProduct::where('product_id',$pd['product_id'])->first();
                    $all_branches = false;
                    $central_rate = true;
                    if(auth()->user()->central_rate == 0 && $get_warranty->product_warranty_status == 1)
                    {
                        $all_branches = false;
                        $central_rate = true;
                    }
                    if(auth()->user()->central_rate == 1 && $get_warranty->product_warranty_status == 1)
                    {
                        $all_branches = true;
                        $central_rate = true;
                    }
                    if((auth()->user()->central_rate == 0 || auth()->user()->central_rate == "") && 
                        ($get_warranty->product_warranty_status == 0 || $get_warranty->product_warranty_status == "")
                    ){
                        $all_branches = false;
                        $central_rate = true;
                    }
                    /*if($get_branch_central_rate->branch_size == 1 && auth()->user()->central_rate == 0)
                    {
                        $all_branches = false;
                        $central_rate = false;
                    }*/
                    
                    TblPurcChangeRateDtl::where('change_rate_id',$form_id)->delete();
                    if(in_array('all',$request->branch_ids)){
                        $all_branches = true;
                    }

                    if($all_branches)
                    {
                        $branch_ids = TblSoftBranch::where('branch_size', 1)->pluck('branch_id')->toArray();
                    }else{
                        $branch_ids = $request->branch_ids;
                    }

                    foreach ($branch_ids as $branch_id)
                    {
                        $branch = TblSoftBranch::where('branch_id',$branch_id)->first();
                        TblPurcChangeRateBranches::create([
                            'change_rate_branch_id' => Utilities::uuid(),
                            'change_rate_id' => $changeRate->change_rate_id,
                            'branch_id' => $branch_id,
                        ]);
                        foreach($request->pd as $pd)
                        {
                            TblPurcChangeRateDtl::create([
                                'change_rate_dtl_id' =>  Utilities::uuid(),
                                'change_rate_id' =>  $changeRate->change_rate_id,
                                'product_id' =>  $pd['product_id'],
                                'product_barcode_id' =>  $pd['product_barcode_id'],
                                'product_barcode_barcode' =>  $pd['pd_barcode'],
                                /*'uom_id'  => $pd['uom_id'],*/
                            /* 'change_rate_dtl_packing'  =>  $pd['pd_packing'],*/
                                'old_current_tp' => Utilities::NumFormat($pd['current_tp']),
                                'old_last_tp' => Utilities::NumFormat($pd['last_tp']),
                                'old_sale_rate' => Utilities::NumFormat($pd['sale_rate']),
                                'old_gp_amount' => Utilities::NumFormat($pd['gp_amount']),
                                'old_gp_perc' => Utilities::NumFormat($pd['gp_perc']),
                                'old_mrp' => Utilities::NumFormat($pd['mrp']),
                                'old_whole_sale_rate' => Utilities::NumFormat($pd['whole_sale_rate']),
                                'current_tp' => Utilities::NumFormat($pd['new_tp']),
                                'sale_rate' => Utilities::NumFormat($pd['new_sale_rate']),
                                'gp_amount' => Utilities::NumFormat($pd['new_gp_amount']),
                                'gp_perc' => Utilities::NumFormat($pd['new_gp_perc']),
                                'mrp' => Utilities::NumFormat($pd['new_mrp']),
                                'whole_sale_rate' => Utilities::NumFormat($pd['new_whole_sale_rate']),
                            ]);

                            $barcodeList = TblPurcProductBarcode::where('product_id',$pd['product_id'])
                                ->get(['product_id','product_barcode_id','business_id','product_barcode_barcode']);
                                
                            foreach ($barcodeList as $item)
                            {
                                $purcUpdate = TblPurcProductBarcodePurchRate::where('product_id',$item->product_id)
                                    ->where('product_barcode_id',$item->product_barcode_id)
                                    ->where('business_id',$branch->business_id)
                                    ->where('branch_id',$branch_id)->first();
                                
                                
                                if(!empty($purcUpdate)){
                                    $old_sale_rate = $purcUpdate->sale_rate;
                                    /*$new_sale_rate = $purcUpdate->sale_rate;
                                    if($central_rate)
                                    {
                                        $new_sale_rate = $pd['new_sale_rate'];
                                    }*/
                                   
                                    $old_net_tp = $purcUpdate->net_tp;
                                    $old_updated_at = $purcUpdate->updated_at;

                                    $purcUpdateData = [];
                                    if(!empty($pd['new_tp'])){
                                        $purcUpdateData['product_barcode_cost_rate'] = Utilities::NumFormat($pd['new_tp']);
                                    }
                                    if(!empty($pd['new_sale_rate']))
                                    {
                                        $purcUpdateData['sale_rate'] = Utilities::NumFormat($pd['new_sale_rate']);
                                    }
                                    if(!empty($pd['new_whole_sale_rate'])){
                                        $purcUpdateData['whole_sale_rate'] = Utilities::NumFormat($pd['new_whole_sale_rate']);
                                    }
                                    if(!empty($pd['new_gp_perc'])){
                                        $purcUpdateData['gp_perc'] = Utilities::NumFormat($pd['new_gp_perc']);
                                    }
                                    if(!empty($pd['new_gp_amount'])){
                                        $purcUpdateData['gp_amount'] = Utilities::NumFormat($pd['new_gp_amount']);
                                    }
                                    if(!empty($pd['new_mrp'])){
                                        $purcUpdateData['mrp'] = Utilities::NumFormat($pd['new_mrp']);
                                    }
                                    if(!empty($pd['current_tp'])){
                                        $purcUpdateData['last_cost_rate'] = Utilities::NumFormat($pd['current_tp']);
                                    }
                                    $purcUpdate->update($purcUpdateData);
                                    
                                }else{
                                    $purcUpdate = TblPurcProductBarcodePurchRate::create([
                                        'product_barcode_purch_id' => Utilities::uuid(),
                                        'product_id' => $item->product_id,
                                        'product_barcode_id' => $item->product_barcode_id,
                                        'product_barcode_barcode' => $item->product_barcode_barcode,
                                        'product_barcode_cost_rate' => Utilities::NumFormat($pd['new_tp']),
                                        'sale_rate' => Utilities::NumFormat($pd['new_sale_rate']),
                                        'whole_sale_rate' => Utilities::NumFormat($pd['new_whole_sale_rate']),
                                        'gp_perc' => Utilities::NumFormat($pd['new_gp_perc']),
                                        'gp_amount' => Utilities::NumFormat($pd['new_gp_amount']),
                                        'mrp' => Utilities::NumFormat($pd['new_mrp']),
                                        'business_id' => $branch->business_id,
                                        'company_id' => $branch->company_id,
                                        'branch_id' => $branch_id,
                                    ]);
                                    $old_sale_rate = "";
                                    $old_net_tp = "";
                                    $old_updated_at = "";
                                }

                                /* start=> add product log */
                                $req = [
                                    "document_id" => $changeRate->change_rate_id,
                                    "product_barcode_purch_id" => $purcUpdate->product_barcode_purch_id,
                                    "product_id" => $item->product_id,
                                    "product_barcode_id" => $item->product_barcode_id,
                                    "product_barcode_barcode" => $item->product_barcode_barcode,
                                    "product_barcode_cost_rate" => Utilities::NumFormat($pd['new_tp']),
                                    "sale_rate" => Utilities::NumFormat($pd['new_sale_rate']),
                                    "whole_sale_rate" => Utilities::NumFormat($pd['new_whole_sale_rate']),
                                    "gp_perc" => Utilities::NumFormat($pd['new_gp_perc']),
                                    "gp_amount" => Utilities::NumFormat($pd['new_gp_amount']),
                                    "mrp" => Utilities::NumFormat($pd['new_mrp']),
                                    "business_id" => $branch->business_id,
                                    "company_id" => $branch->company_id,
                                    "branch_id" => $branch_id,
                                    "user_id" => auth()->user()->id,
                                    "old_sale_rate" => $old_sale_rate,
                                    "old_net_tp" => $old_net_tp,
                                    "old_created_date" => date('Y-m-d H:i:s', strtotime($old_updated_at)),
                                    "activity_form_type" => "change_rate",
                                    "activity_form_action" => isset($new)?"create":"update",
                                ];

                                
                                $logRate = new ProductCardController();
                                $return = $logRate->storeRateLog($req);
                                if(!isset($return->original['status']) && $return->original['status'] != 'success'){
                                    return $this->jsonErrorResponse($data, "Rate log not update...", 200);
                                }
                                /* end=> add product log */
                            }

                            TblPurcProduct::where('product_id',$pd['product_id'])->update([
                                'update_id' => Utilities::uuid()
                            ]);
                        }
                    }
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
            $data['redirect'] = '/'.self::$redirect_url.$this->prefixCreatePage;
            return $this->jsonSuccessResponse($data, trans('message.create'), 200);
        }
    }

    public function getGRNData(Request $request , $id = null){
        $data = [];

        if(!isset($id)){
            return $this->jsonErrorResponse($data, 'Something went wrong', 200);
        }

        DB::beginTransaction();
        try {
            $data['grn'] = TblPurcGrn::with('grn_dtl','supplier')->where('grn_id',$id)->where(Utilities::currentBCB())->first();

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
            return $this->jsonSuccessResponse($data, 'Data loaded', 200);
        }else{
            return $this->jsonErrorResponse($data, 'Something will be wrong', 200);
        }
    }

    public function getStockReceivingData(Request $request , $id = null){
        $data = [];

        if(!isset($id)){
            return $this->jsonErrorResponse($data, 'Something went wrong', 200);
        }

        DB::beginTransaction();
        try {
            
        //$data['str'] = TblInveStock::with('stock_dtls','supplier')->where('stock_id',$id)->where(Utilities::currentBCB())->first();
        $data['str'] = ViewStock::where('stock_id',$id)->where(Utilities::currentBCB())->orderBy('stock_dtl_sr_no')->get();
    } catch (QueryException $e) {
            DB::rollback();
            return $this->jsonErrorResponse($data, $e->getMessage(), 200);
        }
        DB::commit();
        if(isset($id)){
            return $this->jsonSuccessResponse($data, 'Data loaded', 200);
        }else{
            return $this->jsonErrorResponse($data, 'Something will be wrong', 200);
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

    public function print(Request $request, $id)
    {
        $data = [];
        $data['title'] = 'Change Rate';
        if(TblPurcChangeRate::where('change_rate_id','LIKE',$id)->where(Utilities::currentBCB())->exists()){
            $data['permission'] = self::$menu_dtl_id.'-print';
            $data['id'] = $id;
            $data['current'] = TblPurcChangeRate::with('change_rate_dtl')->where(Utilities::currentBCB())->where('change_rate_id',$id)->first();
        }else{
            abort('404');
        }
        return view('prints.change_rate_print', compact('data'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
