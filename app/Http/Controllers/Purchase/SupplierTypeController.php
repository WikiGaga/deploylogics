<?php

namespace App\Http\Controllers\Purchase;

use App\Http\Controllers\Controller;
use App\Library\Utilities;
use App\Models\TblPurcSupplier;
use App\Models\TblPurcSupplierType;
use App\Models\TblAccCoa;
use Illuminate\Http\Request;

// db and Validator
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class SupplierTypeController extends Controller
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
    public static $page_title = 'Vendor Group';
    public static $redirect_url = 'supplier-type';
    public static $menu_dtl_id = '44';


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
            if(TblPurcSupplierType::where('supplier_type_id','LIKE',$id)->exists()){
                $data['page_data'] = array_merge($data['page_data'], Utilities::editForm());
                $data['permission'] = self::$menu_dtl_id.'-edit';
                $data['id'] = $id;
                $data['current'] = TblPurcSupplierType::where(Utilities::currentBC())->where('supplier_type_id',$id)->first();
            }else{
                abort('404');
            }
        }else{
            $data['permission'] = self::$menu_dtl_id.'-create';
            $data['page_data'] = array_merge($data['page_data'], Utilities::newForm());
        }
        return view('purchase.supplier_type.form',compact('data'));
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
            'name' => 'required|max:255'
        ]);
        if ($validator->fails()) {
            $data['validator_errors'] = $validator->errors();
            return $this->jsonErrorResponse($data, trans('message.required_fields'), 422);
        }
        DB::beginTransaction();
        try{
            $level_no = 3;
            $supplier_group = TblAccCoa::where('chart_account_id',Session::get('dataSession')->supplier_group)->where(Utilities::currentBC())->first('chart_code');
            //$parent_account_code = "3-01-00-0000";
            $parent_account_code = $supplier_group->chart_code;
            $business_id = auth()->user()->business_id;
            $company_id = auth()->user()->company_id;
            $branch_id = auth()->user()->branch_id;
            $user_id = auth()->user()->id;
            $chart_name = $request->name;
            if(isset($id)){
                $sup_type = TblPurcSupplierType::where(Utilities::currentBC())->where('supplier_type_id',$id)->first();
                $acc_id = $sup_type->supplier_type_account_id;
                $this->proPurcChartUpdate($business_id,$company_id,$branch_id,$chart_name,$acc_id);
            }else{
                $supplier_type_account_id = $this->proPurcChartInsert($level_no,$parent_account_code,$business_id,$company_id,$branch_id,$user_id,$chart_name);
                $sup_type = new TblPurcSupplierType();
                $sup_type->supplier_type_id = Utilities::uuid();
                $sup_type->supplier_type_account_id = $supplier_type_account_id;
            }
            $form_id = $sup_type->supplier_type_id;
            $sup_type->supplier_type_name = $request->name;
            $sup_type->supplier_type_entry_status = isset($request->supplier_type_entry_status)?"1":"0";
            $sup_type->business_id = auth()->user()->business_id;
            $sup_type->company_id = auth()->user()->company_id;
            $sup_type->branch_id = auth()->user()->branch_id;
            $sup_type->supplier_type_user_id = auth()->user()->id;
            $sup_type->save();

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
            return $this->jsonErrorResponse($data, $e->getLine().":".$e->getMessage(), 200);
        }
        DB::commit();
        if(isset($id)){
            $data = array_merge($data, Utilities::returnJsonEditForm());
            $data['redirect'] = $this->prefixIndexPage.self::$redirect_url;
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
            if(!TblPurcSupplier::where(Utilities::currentBC())->where('supplier_type',$id)->exists()){
                $sup_type = TblPurcSupplierType::where(Utilities::currentBC())->where('supplier_type_id',$id)->first();
                $business_id = auth()->user()->business_id;
                $company_id = auth()->user()->company_id;
                $branch_id = auth()->user()->branch_id;
                $acc_id = $sup_type->supplier_type_account_id;
                $this->proPurcChartDelete($business_id,$company_id,$branch_id,$acc_id);
                $sup_type->delete();
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
