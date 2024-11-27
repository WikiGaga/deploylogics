<?php

namespace App\Http\Controllers\Api\Inventory;

use App\Http\Controllers\ApiController;
use App\Library\ApiUtilities;
use App\Models\TblDefiStore;
use App\Models\TblInveStock;
use App\Models\TblInveStockDtl;
use App\Models\TblPurcProductBarcode;
use App\Models\TblPurcProductBarcodeDtl;
use App\Models\TblPurcSupplier;
use App\Models\TblSoftBranch;
use App\Models\ViewInveDisplayLocation;
use Illuminate\Http\Request;

// db and Validator
use Illuminate\Validation\Rule;
use Validator;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Importer;
use Session;

class StockController extends ApiController
{
    public function getLocationsByStore(Request $request)
    {
        $data = [];
       // DB::beginTransaction();
        try{

            $business_id = $request->business_id;
            $branch_id = $request->branch_id;
            $currentBCB = [
                ['business_id', $business_id],
                ['company_id',$business_id],
                ['branch_id',$branch_id]
            ];


            if(TblDefiStore::where('store_id',$request->store_id)->where('branch_id',$branch_id)->exists()){

                $data['locations'] =  ViewInveDisplayLocation::where('store_id',$request->store_id)->orderBy('display_location_name_string')->get(['display_location_id','display_location_name_string']);

            }

        }catch (Exception $e) {
            DB::rollback();
            return $this->ApiJsonErrorResponse($data, $e->getMessage());
        }

       // DB::commit();

        return $this->ApiJsonSuccessResponse($data,"Get data stock locations");

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
    public function create($type,$id = null)
    {
        /*
         * Opening Stock = OS
         * Stock Transfer = ST
         * Internal Stock Transfer = ST
         * */

        switch ($type){
            case 'opening-stock': {
                $data['title'] = 'Opening Stock';
                $data['stock_code_type'] = 'os';
                break;
            }
            case 'stock-transfer': {
                $data['title'] = 'Stock Transfer';
                $data['stock_code_type'] = 'st';
                break;
            }
            case 'internal-stock-transfer': {
                $data['title'] = 'Internal Stock Transfer';
                $data['stock_code_type'] = 'ist';
                break;
            }
        }
        $data['form_type'] = $type;
        if(isset($id)){
            if(TblInveStock::where('stock_id','LIKE',$id)->where(ApiUtilities::currentBCB())->exists()){
                $data['action'] = 'edit';
                $data['current'] = TblInveStock::with('stock_dtls','supplier')->where(ApiUtilities::currentBCB())->where('stock_id',$id)->where('stock_code_type',$data['stock_code_type'])->first();
                $data['display_location'] = ViewInveDisplayLocation::select('display_location_id','display_location_name_string')->where('branch_id',Session::get('ApiDataSession')->branch_id)->where('store_id',$data['current']->stock_store_from_id)->orderBy('display_location_name_string')->get();
            }else{
                abort('404');
            }
        }else{
            $data['action'] = 'save';
            $doc_data = [
                'biz_type'          => 'branch',
                'model'             => 'TblInveStock',
                'code_field'        => 'stock_code',
                'code_prefix'       => strtoupper($data['stock_code_type']),
                'code_type_field'   => 'stock_code_type',
                'code_type'         => $data['stock_code_type']

            ];
            $data['stock_code'] = ApiUtilities::documentCode($doc_data);
        }

        $data['store'] = TblDefiStore::select('store_id','store_name')->where(ApiUtilities::currentBCB())->get();
        $data['branch'] = TblSoftBranch::select('branch_id','branch_short_name')->where(ApiUtilities::currentBC())->where('branch_id','!=',Session::get('ApiDataSession')->branch_id)->get();
        $data['rate_by'] = config('constants.rate_by');
        return $this->ApiJsonSuccessResponse($data,'form data');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request,$type, $id = null)
    {

        $data = [];
        $errData = [];
        $valid = [
            'stock_date' => 'required|date_format:d-m-Y',
            'store' => 'nullable|numeric',
            'store_to' => 'nullable|numeric',
            'pd.*.product_id' => 'nullable|numeric',
            'pd.*.product_barcode_id' => 'nullable|numeric',
            'pd.*.uom_id' => 'nullable|numeric',
            'pd.*.pd_barcode' => 'nullable|max:100',
            'pd.*.demand_qty' => 'nullable|numeric',
            'pd.*.quantity' => 'nullable|numeric',
            'pd.*.batch_no' => 'nullable|max:20',
            'stock_remarks' => 'nullable|max:100',
        ];
        if($request->stock_code_type == 'os'){
            $valid['stock_location_id'] = 'required|numeric|not_in:0';
            $valid['store'] = 'required|numeric|not_in:0';
        }
        //dd($valid);
        $validator = Validator::make($request->all(), $valid);
        if ($validator->fails()) {
            return $this->ApiJsonErrorResponse($data,trans('message.required_fields'));
        }
        if(isset($request->selected_barcode_rate) && !empty($request->selected_barcode_rate)){
            $rate_by = config('constants.rate_by');
            $rate_by=array_flip($rate_by);
            if(!in_array($request->selected_barcode_rate,$rate_by)){
                return $this->ApiJsonErrorResponse($data,trans('message.required_fields'));
            }
        }
        DB::beginTransaction();
        try{
            $formType = $request->stock_code_type;
            if(isset($id)){
                $stock = TblInveStock::where('stock_id',$id)->first();
            }else{
                $stock = new TblInveStock();
                $stock->stock_id =  ApiUtilities::uuid();
                $doc_data = [
                    'biz_type'          => 'branch',
                    'model'             => 'TblInveStock',
                    'code_field'        => 'stock_code',
                    'code_prefix'       => strtoupper($formType),
                    'code_type_field'   => 'stock_code_type',
                    'code_type'         => $formType

                ];
                $stock->stock_code =  ApiUtilities::documentCode($doc_data);
            }

            $stock->stock_code_type =  $formType;
            $stock->stock_date =   date('Y-m-d', strtotime($request->stock_date));
            $stock->stock_menu_id =  $request->stock_menu_id;
            $stock->stock_total_qty =  0;
            $stock->stock_total_amount =  0;
            $stock->product_id = $request->f_product_id;
            $stock->product_barcode_id = $request->f_product_barcode_id;
            $stock->assamble_qty = $request->assamble_qty;
            $stock->stock_store_from_id =  isset($request->store)?$request->store:"";
            $stock->stock_store_to_id =  isset($request->store_to)?$request->store_to:"";
            $stock->stock_branch_from_id =  isset($request->branch)?$request->branch:"";
            $stock->stock_branch_to_id =  isset($request->branch_to)?$request->branch_to:"";
            $stock->stock_rate_by =  isset($request->selected_barcode_rate)?$request->selected_barcode_rate:"";
            $stock->stock_remarks = $request->stock_remarks;
            $stock->stock_request_id = isset($request->stock_from_id)?$request->stock_from_id:'';
            $stock->stock_location_id = isset($request->stock_location_id)?$request->stock_location_id:'';
            $stock->stock_entry_status = 1;
            $stock->supplier_id = isset($request->supplier_id)?$request->supplier_id:'';
            $stock->business_id = auth()->user()->business_id;
            $stock->company_id = auth()->user()->company_id;
            $stock->branch_id = auth()->user()->branch_id;
            $stock->stock_user_id = auth()->user()->id;
            $stock->save();

            $del_Dtls = TblInveStockDtl::where('stock_id',$id)->get();
            foreach ($del_Dtls as $del_Dtl){
                TblInveStockDtl::where('stock_dtl_id',$del_Dtl->stock_dtl_id)->delete();
            }
            $stock_total_qty = 0;
            $stock_total_amount = 0;
            if(isset($request->pd)){
                $key = 1;
                foreach($request->pd as $pd){
                    $errData = $pd;

                    $dtl = new TblInveStockDtl();
                    $dtl->stock_id =  $stock->stock_id;
                    $dtl->stock_dtl_id =  ApiUtilities::uuid();
                    $dtl->stock_dtl_sr_no =  $key++;
                    $dtl->product_id =  $pd['product_id'];
                    $dtl->product_barcode_id =  $pd['product_barcode_id'];
                    $dtl->product_barcode_barcode =  $pd['pd_barcode'];
                    $dtl->uom_id =  $pd['uom_id'];
                    $dtl->stock_dtl_packing =  $pd['pd_packing'];
                    $dtl->stock_dtl_qty_base_unit =  (isset($pd['pd_packing'])?$pd['pd_packing']:'0') * (isset($pd['quantity'])?$pd['quantity']:'0') ;
                    $dtl->stock_dtl_rate =  isset($pd['rate'])?$this->addNo($pd['rate']):"";
                    $dtl->stock_dtl_purc_rate =  isset($pd['purc_rate'])?$this->addNo($pd['purc_rate']):"";
                    $dtl->stock_dtl_demand_quantity =  isset($pd['demand_qty'])?$pd['demand_qty']:"";
                    $dtl->stock_dtl_stock_transfer_qty =  isset($pd['stock_transfer_qty'])?$pd['stock_transfer_qty']:"";
                    $dtl->stock_dtl_quantity =  $pd['quantity'];
                    $dtl->stock_dtl_batch_no =  isset($pd['batch_no'])?$pd['batch_no']:"";
                    $dtl->stock_dtl_store =  isset($pd['store'])?$pd['store']:"";
                    $dtl->stock_dtl_amount =  isset($pd['amount'])?$this->addNo($pd['amount']):"";
                    $dtl->stock_dtl_stock_quantity =  isset($pd['stock_quantity'])?$pd['stock_quantity']:"";
                    $dtl->stock_dtl_physical_quantity =  isset($pd['physical_quantity'])?$pd['physical_quantity']:"";
                    $dtl->stock_dtl_production_date =  isset($pd['production_date'])?date('Y-m-d', strtotime($pd['production_date'])):"";
                    $dtl->stock_dtl_expiry_date =  isset($pd['expiry_date'])?date('Y-m-d', strtotime($pd['expiry_date'])):"";
                    $dtl->business_id = Session::get('ApiDataSession')->business_id;
                    $dtl->company_id = Session::get('ApiDataSession')->company_id;
                    $dtl->branch_id = Session::get('ApiDataSession')->branch_id;
                    $dtl->dtl_user_id = Session::get('ApiDataSession')->user_id;
                    $stock_total_qty += isset($pd['quantity'])?$this->addNo($pd['quantity']):0;
                    $stock_total_amount += isset($pd['amount'])?$this->addNo($pd['amount']):0;
                    $dtl->save();
                    if($request->store == 2 && $formType == 'os'){
                        // if select store *showroom* and Stock Location
                        // then update product with this stock location and user
                        $branches = TblSoftBranch::where('branch_active_status',1)->get();
                        $barcodes = TblPurcProductBarcode::where('product_id',$pd['product_id'])->get();
                        foreach($barcodes as $barcode){
                            foreach($branches as $branch){
                                if($barcode->product_barcode_id == $pd['product_barcode_id'] && $branch->branch_id == Session::get('ApiDataSession')->branch_id){
                                    if(TblPurcProductBarcodeDtl::where('product_barcode_id',$pd['product_barcode_id'])->where('branch_id',Session::get('ApiDataSession')->branch_id)->exists()){
                                        $barcodeDtl = TblPurcProductBarcodeDtl::where('product_barcode_id',$pd['product_barcode_id'])
                                            ->where('branch_id',Session::get('ApiDataSession')->branch_id)->first();
                                    }else{
                                        $barcodeDtl = new TblPurcProductBarcodeDtl();
                                        $barcodeDtl->product_barcode_dtl_id = ApiUtilities::uuid();
                                        $barcodeDtl->product_barcode_id = $pd['product_barcode_id'];
                                        $barcodeDtl->branch_id = Session::get('ApiDataSession')->branch_id;
                                        $barcodeDtl->product_barcode_stock_limit_neg_stock = 0;
                                        $barcodeDtl->product_barcode_stock_limit_limit_apply = 0;
                                        $barcodeDtl->product_barcode_stock_limit_status = 0;
                                        $barcodeDtl->product_barcode_tax_apply =  0;
                                    }
                                    $barcodeDtl->product_barcode_shelf_stock_location = $request->stock_location_id;
                                    $barcodeDtl->product_barcode_shelf_stock_sales_man = Session::get('ApiDataSession')->user_id;
                                    $barcodeDtl->save();
                                }else{
                                    TblPurcProductBarcodeDtl::create([
                                        'product_barcode_dtl_id' => ApiUtilities::uuid(),
                                        'product_barcode_id' => $barcode->product_barcode_id,
                                        'branch_id' => $branch->branch_id,
                                        'product_barcode_shelf_stock_location' => 0,
                                        'product_barcode_shelf_stock_sales_man' => '',
                                        'product_barcode_stock_limit_neg_stock' => 0,
                                        'product_barcode_stock_limit_limit_apply' => 0,
                                        'product_barcode_stock_limit_status' => 0,
                                        'product_barcode_tax_apply' => 0,
                                    ]);
                                }
                            }
                        }
                    }
                }
            }
            $stock = TblInveStock::where('stock_id',$stock->stock_id)->first();
            $stock->stock_total_qty =  $stock_total_qty;
            $stock->stock_total_amount =  $stock_total_amount;
            $stock->save();
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
    public function destroy($type,$id)
    {
        $data = [];
        DB::beginTransaction();
        try{
            $stock= TblInveStock::where('stock_id',$id)->first();
            $stock->stock_dtls()->delete();
            $stock->delete();

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
        return $this->ApiJsonSuccessResponse($data, trans('message.delete'), 200);
    }

}
