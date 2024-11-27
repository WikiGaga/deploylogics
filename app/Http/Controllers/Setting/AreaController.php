<?php

namespace App\Http\Controllers\Setting;

use Exception;
use App\Library\Utilities;
use App\Models\TblDefiArea;
use App\Models\TblDefiCity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use Validator;

class AreaController extends Controller
{

    public static $page_title = 'Area';
    public static $redirect_url = 'area';
    public static $menu_dtl_id = '208';


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
            if(TblDefiArea::where('area_id','LIKE',$id)->exists()){
                $data['page_data'] = array_merge($data['page_data'], Utilities::editForm());
                $data['permission'] = self::$menu_dtl_id.'-edit';
                $data['id'] = $id;
                $data['current'] = TblDefiArea::with('city')->where('area_id',$id)->first();
            }else{
                abort('404');
            }
        }else{
            $data['permission'] = self::$menu_dtl_id.'-create';
            $data['page_data'] = array_merge($data['page_data'], Utilities::newForm());
        }
        $data['city'] = TblDefiCity::where('city_entry_status',1)->get();
        return view('setting.area.form',compact('data'));
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
            'name' => 'required|max:100'
        ]);
        if ($validator->fails()) {
            $data['validator_errors'] = $validator->errors();
            return $this->jsonErrorResponse($data, trans('message.required_fields'), 422);
        }
        if(!isset($id)){
            if(TblDefiArea::where('area_name','LIKE',$request->name)->where('city_id', $request->area_city)->where('business_id', auth()->user()->business_id)->exists()){
                return $this->jsonErrorResponse($data, trans('message.duplicate_record'), 422);
            }
        }
        DB::beginTransaction();
        try{
            if(isset($id)){
                $area = TblDefiArea::where('area_id',$id)->first();
            }else{
                $area = new TblDefiArea();
                $area->area_id = Utilities::uuid();
            }
            $form_id = $area->area_id;
            $area->area_name = $request->name;
            $area->arabic_name = $request->arabic_name;
            $area->city_id = $request->area_city;
            $area->serial = $request->serial;
            $area->area_entry_status = isset($request->area_entry_status)?"1":"0";
            $area->area_default_status = isset($request->area_default_status)?"1":"0";
            $area->business_id = auth()->user()->business_id;
            $area->company_id = auth()->user()->company_id;
            $area->branch_id = auth()->user()->branch_id;
            $area->area_user_id = auth()->user()->id;
            $area->save();

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

    public function getAreaByCity(Request $request){

        $city_id = $request->city_id;
        $areas = TblDefiArea::where('area_entry_status' , 1)->where('city_id' , $city_id)->get();
        if(count($areas) > 0){
            return $this->jsonSuccessResponse($areas , 'Success');
        }else{
            return $this->jsonErrorResponse([],'No Area Found');
        }
        
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
            $area = TblDefiArea::where('area_id',$id)->first();
            $area->delete();
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
