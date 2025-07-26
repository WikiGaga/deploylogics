<?php

namespace App\Http\Controllers\Purchase;

use App\Http\Controllers\Controller;
use App\Models\TblPurcGroupItem;
use App\Models\User;
use App\Models\ViewPurcGroupItem;
use App\Models\TblSoftProductTypeGroup;
use App\Models\TblPurcProduct;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Webpatser\Uuid\Uuid;
use Illuminate\Support\Facades\DB;
use App\Library\Utilities;
use Illuminate\Validation\Rule;
use Validator;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class GroupitemController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public static $page_title = 'Product Group';
    public static $redirect_url = 'product-group';
    public static $menu_dtl_id = '2';

    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request, $id = null)
    {
        $data['page_data'] = [];
        $data['page_data']['title'] = self::$page_title;
        $data['page_data']['path_index'] = $this->prefixIndexPage.self::$redirect_url;;
        $data['page_data']['create'] = '/'.self::$redirect_url.$this->prefixCreatePage;
        if(isset($id)){
            if(TblPurcGroupItem::where('group_item_id','LIKE',$id)->exists()){
                $data['page_data'] = array_merge($data['page_data'], Utilities::editForm());
                $data['permission'] = self::$menu_dtl_id.'-edit';
                $data['id'] = $id;
                $data['current'] = TblPurcGroupItem::where('group_item_id',$id)->where(Utilities::currentBC())->first();
                $data['code_string'] = ViewPurcGroupItem::where(Utilities::currentBC())->select('group_item_name_code_string')->where('group_item_id',$data['current']->parent_group_id)->first();
            }else{
                abort('404');
            }
        }else{
            $data['permission'] = self::$menu_dtl_id.'-create';
            $data['page_data'] = array_merge($data['page_data'], Utilities::newForm());
        }
        $data['producttypegroup'] = TblSoftProductTypeGroup::where(Utilities::currentBC())->orderBy('product_type_group_name','ASC')->get();

        if($request->type == 'group_item_tree'){
            $data['main_id'] = $request->main_id;
            $data['parent_id'] = $request->parent_id;
            $data['level'] = $request->level;
            $code = $this->PGroupCode($request->parent_id);
            $data['code'] = $code->original['parent']->group_item_name_code_string.'-'.$code->original['code'];
            $data['parent'] = ViewPurcGroupItem::where('group_item_id',$request->parent_id)->where(Utilities::currentBC())->first();
            $data['page_data']['create'] = "";
            $data['page_data']['path_index'] = "";
            return view('purchase.product_tree.product_group_form',compact('data'));
        }else{
            $data['all'] = TblPurcGroupItem::where(Utilities::currentBC())->where('group_item_entry_status',1)->get();
            $data['parent'] = ViewPurcGroupItem::where(Utilities::currentBC())->orderBy('group_item_name_string')->get();
            $data['page_data']['path_index'] =  $this->prefixIndexPage.self::$redirect_url;
            return view('purchase.product_group.form', compact('data'));
        }
    }


    public function PGroupCode($id){
        $data['parent'] = ViewPurcGroupItem::whereIn('group_item_level',[1,2])->where(Utilities::currentBC())->where('group_item_id',$id)->first();
        if(empty($data['parent'])){
            $data = ['status'=>'error','msg'=>'Parent Group is not correct'];
            return response()->json($data);
        }
        $data['code'] = (TblPurcGroupItem::where(Utilities::currentBC())->select('group_item_code')->where('parent_group_id','=',$id)->max('group_item_code'))+1;
        $data['status'] = 'success';
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
        //dd($id);
        //dd($request->toArray());
        $data = [];
        $validator = Validator::make($request->all(), [
            'group_item_name' => 'required|max:100',
            'group_item_mother_language_name' => 'max:100',
            'product_type_group_id' => 'required|numeric',
            'parent_group_id' => 'numeric'
        ]);
        if ($validator->fails()) {
            $data['validator_errors'] = $validator->errors();
            return $this->jsonErrorResponse($data, trans('message.required_fields'), 422);
        }
        DB::beginTransaction();
        try{
            if(isset($id)){
                $GroupItem = TblPurcGroupItem::where('group_item_id',$id)->first();
                $productids = TblPurcProduct::where('group_item_id',$id)
                ->get(['product_id','business_id']);

                foreach($productids as $productid)
                {
                    $vExist = TblPurcProduct::where('group_item_id',$id)
                        ->where('product_id',$productid->product_id);

                    if($vExist->exists()){
                        $vExist->update([
                        'group_item_parent_id'=> $GroupItem->parent_group_id,
                        'update_id'=> Utilities::uuid()
                        ]);
                    }
                }
            }else{
                session()->forget('session_data_GroupItem');
                $GroupItem = new TblPurcGroupItem();
                $GroupItem->group_item_id = Utilities::uuid();
                $pg_id = ($request->parent_group_id != 0) ? $request->parent_group_id : null;
                $code=(TblPurcGroupItem::where(Utilities::currentBC())->select('group_item_code')->where('parent_group_id','=',$pg_id)->max('group_item_code'))+1;
                $GroupItem->group_item_code =$code;

                Session::put('session_group_item', ['parent_group_id' => $pg_id ]);

            }

            $form_id = $GroupItem->group_item_id;
            $GroupItem->group_item_name = $request->group_item_name;
            $GroupItem->product_type_group_id = $request->product_type_group_id;
            //dd($GroupItem->product_type_group_id);
            $GroupItem->group_item_mother_language_name = $request->group_item_mother_language_name;
            $GroupItem->parent_group_id = ($request->parent_group_id != 0) ? $request->parent_group_id : '';
            $GroupItem->group_item_sales_status = isset($request->group_item_sales_status)?1:0;
            $GroupItem->group_item_brand_validation = isset($request->group_item_brand_validation)?1:0;
            $GroupItem->group_item_expiry = isset($request->group_item_expiry)?1:0;
            $GroupItem->group_item_stock_type = isset($request->group_item_stock_type)?1:0;
            $GroupItem->group_item_ref_no = isset($request->group_item_ref_no)?$request->group_item_ref_no:'';
            $GroupItem->group_item_entry_status = 1;
            $GroupItem->business_id = auth()->user()->business_id;
            $GroupItem->company_id = auth()->user()->company_id;
            $GroupItem->branch_id = auth()->user()->branch_id;
            $GroupItem->group_item_user_id = auth()->user()->id;
            $GroupItem->group_item_entry_date_time = Carbon::now();
            $GroupItem->save();

            $getProductData = ViewPurcGroupItem::where('group_item_id',$GroupItem->group_item_id)->first();
            $data['name'] = "[".$getProductData->group_item_name_code_string."] ". $getProductData->group_item_name;
            $data['main_id'] = $getProductData->group_item_id;
            $data['parent_main_id'] = $getProductData->group_parent_item_id;
            $data['level'] = $getProductData->group_item_level;
            $data['code'] = $getProductData->group_item_code;
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
            return $this->jsonSuccessResponse($data, trans('message.update'), 200);
        }else{
            $data = array_merge($data, Utilities::returnJsonNewForm());
            $data['redirect'] = '/'.self::$redirect_url.$this->prefixCreatePage;
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
    public function destroy($id, Request $request)
    {
        $data = [];
        DB::beginTransaction();
        try{
            $GroupItem = TblPurcGroupItem::where(Utilities::currentBC())->where('group_item_id',$id)->withCount('purcProduct')->first();
            if($GroupItem->purc_product_count == 0){
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
