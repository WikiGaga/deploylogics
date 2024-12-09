<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Library\Utilities;
use App\Models\TblInveItemStockOpening;
use App\Models\TblInveItemStockOpeningDtl;
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

class OpeningStockController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public static $page_title = 'Opening Stock';
    public static $redirect_url = 'opening-stock';
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
            if(TblInveItemStockOpening::where('opening_stock_id','LIKE',$id)->exists()){
                $data['page_data'] = array_merge($data['page_data'], Utilities::editForm());
                $data['id'] = $id;
                $data['current'] = TblInveItemStockOpening::with('dtls')->where('opening_stock_id',$id)->first();
            }else{
                abort('404');
            }
        }else{
            $data['page_data'] = array_merge($data['page_data'], Utilities::newForm());
            $data['document_code'] = $this->documentCode(TblInveItemStockOpening::max('opening_stock_code'),'OS');
        }
        $data['store'] = TblDefiStore::get();
        //dd($data);
        return view('inventory.opening_stock.form',compact('data'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $id = null)
    {
        //dd($request->toArray());
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
                $stock= TblInveItemStockOpening::where('opening_stock_id',$id)->first();
            }else{
                $stock = new TblInveItemStockOpening();
                $stock->opening_stock_id = Utilities::uuid();
                $stock->opening_stock_code = $this->documentCode(TblInveItemStockOpening::max('opening_stock_code'),'OS');
            }
            $stock->opening_stock_store = $request->store;
            $stock->opening_stock_date = date('Y-m-d', strtotime($request->stock_date));
            $stock->opening_stock_remarks = $request->stock_remarks;
            $stock->opening_stock_entry_status = 1;
            $stock->business_id = auth()->user()->business_id;
            $stock->company_id = auth()->user()->company_id;
            $stock->branch_id = auth()->user()->branch_id;
            $stock->opening_stock_user_id = auth()->user()->id;
            $stock->save();

            $del_Dtls = TblInveItemStockOpeningDtl::where('opening_stock_id',$id)->get();
            foreach ($del_Dtls as $del_Dtl){
                TblInveItemStockOpeningDtl::where('opening_stock_dtl_id',$del_Dtl->opening_stock_dtl_id)->delete();
            }
            if(isset($request->pd)){
                foreach($request->pd as $pd){
                    $dtl = new TblInveItemStockOpeningDtl();
                    if(isset($id) && isset($pd['opening_stock_dtl_id'])){
                        $dtl->opening_stock_dtl_id = $pd['opening_stock_dtl_id'];
                        $dtl->opening_stock_id = $id;
                    }else{
                        $dtl->opening_stock_dtl_id = Utilities::uuid();
                        $dtl->opening_stock_id = $stock->opening_stock_id;
                    }
                    $dtl->product_id = $pd['product_id'];
                    $dtl->product_barcode_id = $pd['product_barcode_id'];
                    $dtl->uom_id = $pd['uom_id'];
                    $dtl->product_barcode_barcode = $pd['barcode'];
                    $dtl->opening_stock_dtl_packing = $pd['packing'];
                    $dtl->opening_stock_dtl_batch_no = $pd['batch_no'];
                    $dtl->opening_stock_dtl_quantity = $pd['quantity'];
                    $dtl->opening_stock_dtl_rate = $this->addNo($pd['rate']);
                    $dtl->opening_stock_dtl_amount = $this->addNo($pd['amount']);
                    $dtl->business_id = auth()->user()->business_id;
                    $dtl->company_id = auth()->user()->company_id;
                    $dtl->branch_id = auth()->user()->branch_id;
                    $dtl->opening_stock_dtl_user_id = auth()->user()->id;
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

            $stock= TblInveItemStockOpening::where('opening_stock_id',$id)->first();
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
