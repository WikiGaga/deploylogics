<?php

namespace App\Http\Controllers\Setting;

use App\Http\Controllers\Controller;
use App\Models\TblDefiStore;
use App\Models\TblInveDisplayLocation;
use App\Models\ViewInveDisplayLocation;
use App\Models\TblSoftBranch;
use Illuminate\Http\Request;
// db and Validator
use App\Library\Utilities;
use Illuminate\Validation\Rule;
use Validator;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Contracts\Session\Session;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class DisplayLocationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public static $page_title = 'Location';
    public static $redirect_url = 'location';
    public static $menu_dtl_id = '30';
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
            if(TblInveDisplayLocation::where('display_location_id','LIKE',$id)->exists()){
                $data['page_data'] = array_merge($data['page_data'], Utilities::editForm());
                $data['permission'] = self::$menu_dtl_id.'-edit';
                $data['id'] = $id;
                $data['current'] = TblInveDisplayLocation::where('display_location_id',$id)->first();
                $data['parent'] = ViewInveDisplayLocation::where('store_id',$data['current']->store_id)->orderBy('display_location_name_string')->get();
            }else{
                abort('404');
            }
        }else{
            $data['permission'] = self::$menu_dtl_id.'-create';
            $data['page_data'] = array_merge($data['page_data'], Utilities::newForm());
            $sessionStoreID = session('display_location_id');
            if(!empty($sessionStoreID)){
                $data['parent'] = ViewInveDisplayLocation::where('store_id',$sessionStoreID)->orderBy('display_location_name_string')->get();
            }
        }
        $data['store'] = TblDefiStore::where('branch_id',auth()->user()->branch_id)->get();
        return view('setting.location.form', compact('data'));
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
            'name' => 'required|max:100',
            'store_id' => 'required|not_in:0'
        ]);
        if ($validator->fails()) {
            $data['validator_errors'] = $validator->errors();
            return $this->jsonErrorResponse($data, trans('message.required_fields'), 422);
        }
        DB::beginTransaction();
        try{
            if(isset($id)){
                $location = TblInveDisplayLocation::where('display_location_id',$id)->first();
            }else{
                $location = new TblInveDisplayLocation();
                $location->display_location_id = Utilities::uuid();
                $location->store_id = $request->store_id;
                $location->parent_display_location_id = (isset($request->parent_display_location_id) && !empty($request->parent_display_location_id))?$request->parent_display_location_id:"";
            }
            $form_id = $location->display_location_id;
            $location->display_location_name = $request->name;
            $location->display_location_branch = auth()->user()->branch_id;
            $location->display_location_entry_status = isset($request->display_location_entry_status)?"1":"0";
            $location->business_id = auth()->user()->business_id;
            $location->company_id = auth()->user()->company_id;
            $location->branch_id = auth()->user()->branch_id;
            $location->display_location_user_id = auth()->user()->id;
            $location->save();
            // dd($this->getSqlWithBindings($location));
            session(['display_location_id' =>  $location->store_id]);
            session(['parent_display_location_id' => $location->parent_display_location_id]);

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
            $data['redirect'] = '/'.self::$redirect_url.$this->prefixCreatePage.'/'.$form_id;
            return $this->jsonSuccessResponse($data, trans('message.create'), 200);
        }
    }
    public function getStoreLocations(Request $request){
        $data = [];
        try {
            if(TblDefiStore::where('store_id',$request->store_id)->exists()){
                $data = ViewInveDisplayLocation::where('store_id',$request->store_id)->orderBy('display_location_name_string')->get();
            }
        }catch (Exception $e) {
            DB::rollback();
            return $this->jsonErrorResponse($data, $e->getMessage(), 200);
        }
        return $this->jsonSuccessResponse($data, '', 200);
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

            $location = TblInveDisplayLocation::where('display_location_id',$id)->first();
            $location->delete();

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
