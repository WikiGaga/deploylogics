<?php

namespace App\Http\Controllers\Purchase;

use App\Http\Controllers\Controller;
use App\Models\TblPurcStockLocation;
use App\Models\ViewInveDisplayLocation;
use App\Models\User;
use App\Models\TblPurcProductBarcodeDtl;
use Carbon\Carbon;
use Illuminate\Http\Request;
use phpDocumentor\Reflection\Types\Null_;
use Webpatser\Uuid\Uuid;
use Illuminate\Support\Facades\DB;
use App\Library\Utilities;
use Illuminate\Validation\Rule;
use Validator;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class StockLocationController extends Controller
{
    public static $page_title = 'Stock Location';
    public static $redirect_url = 'stock-location';
    public static $menu_dtl_id = '47';

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
        $data['page_data']['title'] = self::$page_title;
        $data['page_data']['path_index'] = $this->prefixIndexPage.self::$redirect_url;
        $data['page_data']['create'] = '/'.self::$redirect_url.$this->prefixCreatePage;
        if(isset($id)){
            if(TblPurcStockLocation::where('stock_location_id','LIKE',$id)->where(Utilities::currentBC())->exists()){
                $data['page_data']['type'] = 'edit';
                $data['page_data']['action'] = 'Update';
                $data['permission'] = self::$menu_dtl_id.'-edit';
                $data['id'] = $id;
                $data['current'] = TblPurcStockLocation::where('stock_location_id',$id)->where(Utilities::currentBC())->first();
            }else{
                abort('404');
            }
        }else{
            $data['permission'] = self::$menu_dtl_id.'-create';
            $data['page_data']['type'] = 'new';
            $data['page_data']['action'] = 'Save';
        }
        $data['display_location'] = ViewInveDisplayLocation::orderBy('display_location_name_string')->get();
        return view('purchase.stock_location.form', compact('data'));
    }


    public function PGroupCode($id){
        $data['parent'] = ViewInveDisplayLocation::where('display_location_id',$id)->first();
        $data['code'] = (TblPurcStockLocation::select('stock_location_code')->where('stock_location_parent_group_id','=',$id)->where(Utilities::currentBC())->max('stock_location_code'))+1;
        return response()->json($data);
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
        $validator = Validator::make($request->all(), [
            'stock_location_name' => 'required|max:100'
        ]);
        if ($validator->fails()) {
            $data['validator_errors'] = $validator->errors();
            return $this->jsonErrorResponse($data, trans('message.required_fields'), 422);
        }
        if($request->parent_group_id == 0){
            return '<script type="text/javascript">alert("hello!");</script>';
        }
        DB::beginTransaction();
        try{
            if(isset($id)){
                $StockLocation = TblPurcStockLocation::where('stock_location_id',$id)->where(Utilities::currentBC())->first();
            }else{
                $StockLocation = new TblPurcStockLocation();
                $StockLocation->stock_location_id = Utilities::uuid();
                $pg_id = ($request->parent_group_id != 0) ? $request->parent_group_id : null;
                $code=(TblPurcStockLocation::select('stock_location_code')->where('stock_location_parent_group_id','=',$pg_id)->where(Utilities::currentBC())->max('stock_location_code'))+1;
                $StockLocation->stock_location_code =$code;
            }
            $form_id = $StockLocation->stock_location_id;
            $StockLocation->stock_location_name = $request->stock_location_name;
            $StockLocation->stock_location_parent_group_id = ($request->parent_group_id != 0) ? $request->parent_group_id : '';
            $StockLocation->stock_location_entry_status = isset($request->stock_location_entry_status) ? 1 : 0;

            $StockLocation->business_id = auth()->user()->business_id;
            $StockLocation->company_id = auth()->user()->company_id;
            $StockLocation->branch_id = auth()->user()->branch_id;
            $StockLocation->stock_location_user_id = auth()->user()->id;
            $StockLocation->stock_location_entry_datetime = Carbon::now();
            $StockLocation->save();

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
            $data['redirect'] = $this->prefixIndexPage.self::$redirect_url;
            return $this->jsonSuccessResponse($data, 'Stock Location successfully updated.', 200);
        }else{
            $data = array_merge($data, Utilities::returnJsonNewForm());
            $data['redirect'] = '/'.self::$redirect_url.$this->prefixCreatePage.'/'.$form_id;
            return $this->jsonSuccessResponse($data,'Stock Location successfully created.',200);
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
    public function destroy($id, Request $request)
    {
        $data = [];
        DB::beginTransaction();
        try{
            $product = TblPurcProductBarcodeDtl::where('product_barcode_shelf_stock_location',$id)->first();
            if($product == null) {
                $GroupItem = TblPurcStockLocation::where('stock_location_id',$id)->where(Utilities::currentBC())->first();
                $GroupItem->delete();
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
