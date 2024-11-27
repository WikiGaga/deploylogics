<?php

namespace App\Http\Controllers\Rent;

use Exception;
use Validator;
use App\Library\Utilities;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Database\QueryException;
use App\Models\Rent\TblRentRentLocation;
use App\Models\Rent\ViewRentRentLocation;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class RentLocationController extends Controller
{
    public static $page_title = 'Rent Location';
    public static $redirect_url = 'rent-location';
    public static $menu_dtl_id = '238';

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
    public function create(Request $request , $id = null)
    {
        $data['page_data'] = [];
        $data['page_data']['title'] = self::$page_title;
        $data['page_data']['path_index'] = $this->prefixIndexPage.self::$redirect_url;;
        $data['page_data']['create'] = '/'.self::$redirect_url.$this->prefixCreatePage;
        if(isset($id)){
            if(TblRentRentLocation::where('rent_location_id','LIKE',$id)->exists()){
                $data['page_data'] = array_merge($data['page_data'], Utilities::editForm());
                $data['permission'] = self::$menu_dtl_id.'-edit';
                $data['id'] = $id;
                $data['current'] = TblRentRentLocation::where('rent_location_id',$id)->where(Utilities::currentBC())->first();
                $data['code_string'] = ViewRentRentLocation::where(Utilities::currentBC())->select('rent_location_name_code_string')->where('rent_location_id',$data['current']->rent_location_parent_id)->first();
            }else{
                abort('404');
            }
        }else{
            $data['permission'] = self::$menu_dtl_id.'-create';
            $data['page_data'] = array_merge($data['page_data'], Utilities::newForm());
        }

        if($request->type == 'rent_location_tree'){
            $data['main_id'] = $request->main_id;
            $data['parent_id'] = $request->parent_id;
            $data['level'] = $request->level;
            $code = $this->PGroupCode($request->parent_id);
            $data['code'] = $code->original['parent']->group_item_name_code_string.'-'.$code->original['code'];
            $data['parent'] = ViewRentRentLocation::where('rent_location_id',$request->parent_id)->where(Utilities::currentBC())->first();
            $data['page_data']['create'] = "";
            $data['page_data']['path_index'] = "";
            return view('rent.rent_location.rent_location_form',compact('data'));
        }else{
            $data['all'] = TblRentRentLocation::where(Utilities::currentBC())->where('rent_location_entry_status',1)->get();
            $data['parent'] = ViewRentRentLocation::where(Utilities::currentBC())->orderBy('rent_location_name_string')->get();
            $data['page_data']['path_index'] =  $this->prefixIndexPage.self::$redirect_url;
            return view('rent.rent_location.form', compact('data'));
        }
    }

    public function RPLocationCode($id){
        $data['parent'] = ViewRentRentLocation::where(Utilities::currentBC())->where('rent_location_id',$id)->first();
        $data['code'] = (TblRentRentLocation::where(Utilities::currentBC())->select('rent_location_code')->where('rent_location_parent_id','=',$id)->max('rent_location_code'))+1;
        return response()->json($data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request , $id = null)
    {
        $data = [];
        $validator = Validator::make($request->all(), [
            'rent_location_name' => 'required|max:100',
            'rent_location_mother_language_name' => 'max:100',
            'rent_location_parent_id' => 'numeric'
        ]);
        if ($validator->fails()) {
            $data['validator_errors'] = $validator->errors();
            return $this->jsonErrorResponse($data, trans('message.required_fields'), 422);
        }
        DB::beginTransaction();
        try{
            if(isset($id)){
                $rentLocation = TblRentRentLocation::where('rent_location_id',$id)->first();
            }else{
                $rentLocation = new TblRentRentLocation();
                $rentLocation->rent_location_id = Utilities::uuid();
                $pl_id = ($request->rent_location_parent_id != 0) ? $request->rent_location_parent_id : null;
                $code=(TblRentRentLocation::where(Utilities::currentBC())->select('rent_location_code')->where('rent_location_parent_id','=',$pl_id)->max('rent_location_code'))+1;
                $rentLocation->rent_location_code =$code;
            }

            $form_id = $rentLocation->rent_location_id;
            $rentLocation->rent_location_name = trim($request->rent_location_name);
            $rentLocation->rent_location_mother_language = $request->rent_location_mother_language_name;
            $rentLocation->rent_location_parent_id = ($request->rent_location_parent_id != 0) ? $request->rent_location_parent_id : '';        
            $rentLocation->rent_location_entry_status = isset($request->rent_location_status)?1:0;
            $rentLocation->rent_location_ref_no = isset($request->rent_location_ref_no)?$request->rent_location_ref_no:'';
            $rentLocation->business_id = auth()->user()->business_id;
            $rentLocation->company_id = auth()->user()->company_id;
            $rentLocation->branch_id = auth()->user()->branch_id;
            $rentLocation->user_id = auth()->user()->id;
            $rentLocation->save();

            $getLocationData = ViewRentRentLocation::where('rent_location_id',$rentLocation->rent_location_id)->first();
            $data['name'] = "[".$getLocationData->rent_location_name_code_string."] ". $getLocationData->rent_location_name;
            $data['main_id'] = $getLocationData->rent_location_id;
            $data['parent_main_id'] = $getLocationData->rent_location_parent_id;
            $data['level'] = $getLocationData->rent_location_level;
            $data['code'] = $getLocationData->rent_location_code;
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
            $rentLocation = TblRentRentLocation::where(Utilities::currentBC())->where('rent_location_id',$id)->first();
            $rentLocation->delete();
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
