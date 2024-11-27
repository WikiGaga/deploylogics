<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Library\Utilities;
use App\Models\TblInveItemStockTransfer;
use App\Models\TblInveItemStockTransferDtl;
use App\Models\TblSoftBranch;
use Illuminate\Http\Request;
// db and Validator
use Illuminate\Validation\Rule;
use Validator;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ItemStockTransferController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public static $page_title = 'Stock Transfer';
    public static $redirect_url = 'stock-transfer';
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
        $data['page_data']['title'] = self::$page_title;
        $data['page_data']['path_index'] = $this->prefixIndexPage.self::$redirect_url;;
        if(isset($id)){
            if(TblInveItemStockTransfer::where('item_stock_transfer_id','LIKE',$id)->exists()){
                $data['page_data'] = array_merge($data['page_data'], Utilities::editForm());
                $data['id'] = $id;
                $data['current'] = TblInveItemStockTransfer::with('dtls')->where('item_stock_transfer_id',$id)->first();
            }else{
                abort('404');
            }
        }else{
            $data['page_data'] = array_merge($data['page_data'], Utilities::newForm());
            $data['document_code'] = $this->documentCode(TblInveItemStockTransfer::max('item_stock_transfer_code'),'ST');
        }
        $data['branch'] = TblSoftBranch::get();
        return view('inventory.stock_transfer.form',compact('data'));
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
        $validator = Validator::make($request->all(), []);
        if ($validator->fails()) {
            $data['validator_errors'] = $validator->errors();
            return $this->jsonErrorResponse($data, trans('message.required_fields'), 422);
        }
        DB::beginTransaction();
        try{
            if(isset($id)){
                $stock= TblInveItemStockTransfer::where('item_stock_transfer_id',$id)->first();
            }else{
                $stock = new TblInveItemStockTransfer();
                $stock->item_stock_transfer_id = Utilities::uuid();
                $stock->item_stock_transfer_code = $this->documentCode(TblInveItemStockTransfer::max('item_stock_transfer_code'),'ST');
            }
            $stock->item_stock_transfer_date = date('Y-m-d', strtotime($request->stock_date));
            $stock->item_stock_transfer_from_store = $request->stock_transfer_from;
            $stock->item_stock_transfer_to_store = $request->stock_transfer_to;
            $stock->item_stock_transfer_remarks = $request->stock_remarks;
            $stock->item_stock_transfer_entry_status = 1;
            $stock->business_id = auth()->user()->business_id;
            $stock->company_id = auth()->user()->company_id;
            $stock->branch_id = auth()->user()->branch_id;
            $stock->item_stock_transfer_user_id = auth()->user()->id;
            $stock->save();

            $del_Dtls = TblInveItemStockTransferDtl::where('item_stock_transfer_id',$id)->get();
            foreach ($del_Dtls as $del_Dtl){
                TblInveItemStockTransferDtl::where('item_stock_transfer_dtl_id',$del_Dtl->item_stock_transfer_dtl_id)->delete();
            }
            if(isset($request->pd)){
                foreach($request->pd as $pd){
                    $dtl = new TblInveItemStockTransferDtl();
                    if(isset($id) && isset($pd['item_stock_transfer_dtl_id'])){
                        $dtl->item_stock_transfer_dtl_id = $pd['item_stock_transfer_dtl_id'];
                        $dtl->item_stock_transfer_id = $id;
                    }else{
                        $dtl->item_stock_transfer_dtl_id = Utilities::uuid();
                        $dtl->item_stock_transfer_id = $stock->item_stock_transfer_id;
                    }
                    $dtl->product_id = $pd['product_id'];
                    $dtl->product_barcode_id = $pd['product_barcode_id'];
                    $dtl->uom_id = $pd['uom_id'];
                    $dtl->product_barcode_barcode = $pd['barcode'];
                    $dtl->item_stock_transfer_dtl_packing = $pd['packing'];
                    $dtl->item_stock_transfer_dtl_qty = $pd['quantity'];
                    $dtl->item_stock_transfer_dtl_rate = $this->addNo($pd['rate']);
                    $dtl->item_stock_transfer_dtl_amount = $this->addNo($pd['amount']);
                    $dtl->business_id = auth()->user()->business_id;
                    $dtl->company_id = auth()->user()->company_id;
                    $dtl->branch_id = auth()->user()->branch_id;
                    $dtl->item_stock_transfer_dtl_user_id = auth()->user()->id;
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
            $data['redirect'] = $this->prefixIndexPage.self::$redirect_url;;
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
    public function destroy($id)
    {
        $data = [];
        DB::beginTransaction();
        try{

            $stock= TblInveItemStockTransfer::where('item_stock_transfer_id',$id)->first();
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
