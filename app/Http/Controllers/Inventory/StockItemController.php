<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Library\Utilities;
use App\Models\TblInveItem;
use App\Models\TblInveItemDtl;
use App\Models\TblDefiStore;
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

class StockItemController extends Controller
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
    public function create($casetype,$id = null)
    {
        $data['page_data'] = [];
        $data['casetype'] = $casetype;
        if($casetype == 'sp'){
            $data['page_data']['title'] = 'Sample Items';
        }else if($casetype == 'dp'){
            $data['page_data']['title'] = 'Damaged Items';
        }else if($casetype == 'ep'){
            $data['page_data']['title'] = 'Expired Items';
        }
        $data['page_data']['path_index'] = '/stock-item/'.$casetype;
        if(isset($id)){
            if(TblInveItem::where('item_id','LIKE',$id)->exists()){
                $data['page_data'] = array_merge($data['page_data'], Utilities::editForm());
                $data['id'] = $id;
                $data['current'] = TblInveItem::with('dtls')->where('item_id',$id)->first();
                $data['document_code'] = $data['current']->item_code;
            }else{
                abort('404');
            }
        }else{
            
            $data['page_data'] = array_merge($data['page_data'], Utilities::newForm());
            $max_no = TblInveItem::where('item_type',$casetype)->max('item_code');
            $data['document_code'] = $this->documentCode($max_no,$casetype);
        }
        $data['store'] = TblDefiStore::get();

        $arr = [
            'biz_type' => 'branch',
            'code' => $data['document_code'],
            'link' => $data['page_data']['create'],
            'table_name' => 'tbl_inve_item',
            'col_id' => 'item_id',
            'col_code' => 'item_code',
            'code_type_field'   => 'item_type',
            'code_type'         => $casetype,
        ];
        $data['switch_entry'] = $this->switchEntry($arr);
        return view('inventory.stock_item.form',compact('data'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request,$casetype, $id = null)
    {
        
        $data = [];
        $validator = Validator::make($request->all(), [
            'store' => 'required'
        ]);
        if ($validator->fails()) {
            $data['validator_errors'] = $validator->errors();
            return $this->jsonErrorResponse($data, trans('message.required_fields'), 422);
        }
        DB::beginTransaction();
        try{
            if(isset($id)){
                $stock= TblInveItem::where('item_id',$id)->where('item_type',$casetype)->first();
            }else{
                $stock = new TblInveItem();
                $stock->item_id = Utilities::uuid();
                $max_no = TblInveItem::where('item_type',$casetype)->max('item_code');
                $stock->item_code = $this->documentCode($max_no,$casetype);
                $stock->item_type = $casetype;
            }
            $stock->item_store = $request->store;
            $stock->item_date = date('Y-m-d', strtotime($request->stock_date));
            $stock->item_remarks = $request->stock_remarks;
            $stock->item_entry_status = 1;
            $stock->business_id = auth()->user()->business_id;
            $stock->company_id = auth()->user()->company_id;
            $stock->branch_id = auth()->user()->branch_id;
            $stock->item_user_id = auth()->user()->id;
            $stock->save();

            $del_Dtls = TblInveItemDtl::where('item_id',$id)->get();
            foreach ($del_Dtls as $del_Dtl){
                TblInveItemDtl::where('item_dtl_id',$del_Dtl->item_dtl_id)->delete();
            }
            if(isset($request->pd)){
                foreach($request->pd as $pd){
                    $dtl = new TblInveItemDtl();
                    if(isset($id) && isset($pd['item_dtl_id'])){
                        $dtl->item_dtl_id = $pd['item_dtl_id'];
                        $dtl->item_id = $id;
                    }else{
                        $dtl->item_dtl_id = Utilities::uuid();
                        $dtl->item_id = $stock->item_id;
                    }
                    $dtl->product_id = $pd['product_id'];
                    $dtl->product_barcode_id = $pd['product_barcode_id'];
                    $dtl->uom_id = $pd['uom_id'];
                    $dtl->product_barcode_barcode = $pd['barcode'];
                    $dtl->item_dtl_packing = $pd['packing'];
                    $dtl->item_dtl_batch_no = $pd['batch_no'];
                    $dtl->item_dtl_quantity = $pd['quantity'];
                    $dtl->item_dtl_rate = $this->addNo($pd['rate']);
                    $dtl->item_dtl_amount = $this->addNo($pd['amount']);
                    $dtl->business_id = auth()->user()->business_id;
                    $dtl->company_id = auth()->user()->company_id;
                    $dtl->branch_id = auth()->user()->branch_id;
                    $dtl->item_dtl_user_id = auth()->user()->id;
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
            return $this->jsonErrorResponse($data, $e->getMessage(), 200);
        }
        DB::commit();
        if(isset($id)){
            $data = array_merge($data, Utilities::returnJsonEditForm());
            $data['redirect'] = '/stock-item/'.$casetype;
            return $this->jsonSuccessResponse($data, trans('message.update'), 200);
        }else{
            $data = array_merge($data, Utilities::returnJsonNewForm());
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

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($casetype,$id)
    {
        $data = [];
        DB::beginTransaction();
        try{

            $stock= TblInveItem::where('item_id',$id)->where('item_type',$casetype)->first();
            $stock->dtls()->delete();
            $stock->delete();

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
