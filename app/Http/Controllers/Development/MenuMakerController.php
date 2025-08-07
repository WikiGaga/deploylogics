<?php

namespace App\Http\Controllers\Development;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\PermissionHeading;
use App\Models\PermissionUser;
use App\Models\TblSoftMenu;
use App\Models\TblSoftMenuDtl;
use Illuminate\Http\Request;
use App\Library\Utilities;
// db and Validator
use Illuminate\Validation\Rule;
use Validator;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;


class MenuMakerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public static $page_title = 'Menu Maker';
    public static $redirect_url = 'menu-maker';
    public static $menu_dtl_id = '39';

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
            if(TblSoftMenuDtl::where('menu_dtl_id','LIKE',$id)->exists()){
                $data['page_data'] = array_merge($data['page_data'], Utilities::editForm());
                $data['permission'] = self::$menu_dtl_id.'-edit';
                $data['id'] = $id;
                $data['current'] = TblSoftMenuDtl::with('permissions')->where('menu_dtl_id',$id)->first();
                $data['permissions'] = [];
                foreach ($data['current']->permissions as $permission){
                    array_push($data['permissions'] ,$permission->display_name);
                }
            }else{
                abort('404');
            }
        }else{
            $data['permission'] = self::$menu_dtl_id.'-create';
            $data['page_data'] = array_merge($data['page_data'], Utilities::newForm());
            $data['code'] = TblSoftMenuDtl::max('menu_dtl_id') + 1;
        }
        $data['menu'] = TblSoftMenu::where('menu_id','!=' , 0)->get();
        $data['views_action'] = PermissionHeading::all();
        return view('development.menu_maker.form',compact('data'));
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
            'menu_dtl_name' => 'required|max:100'
        ]);
        if ($validator->fails()) {
            $data['validator_errors'] = $validator->errors();
            return $this->jsonErrorResponse($data, trans('message.required_fields'), 422);
        }
        DB::beginTransaction();
        try{
            if(isset($id)){
                $menu = TblSoftMenuDtl::where('menu_dtl_id',$id)->first();
            }else{
                $menu = new TblSoftMenuDtl();
                $menu->menu_dtl_id =  TblSoftMenuDtl::max('menu_dtl_id')+1;
            }
            $form_id = $menu->menu_dtl_id;
            $menu->menu_id = $request->menu_id;
            $menu->menu_dtl_name = $request->menu_dtl_name;
            $menu->menu_dtl_link = $request->menu_dtl_link;
            $menu->menu_dtl_table_name = $request->menu_dtl_table_name;
            $menu->menu_dtl_sorting = $request->menu_dtl_sorting;
            $menu->menu_dtl_visibility = 1;
            $menu->business_id = auth()->user()->business_id;
            $menu->company_id = auth()->user()->company_id;
            $menu->branch_id = auth()->user()->branch_id;
            $menu->save();

            if(isset($id)){
                $menu_permissions_arr = [];
                $menu_permissions = Permission::where('menu_dtl_id', $id)->get();
                foreach ($menu_permissions as $permission){
                    array_push($menu_permissions_arr, $permission->display_name);

                    $has = in_array($permission->display_name, $request->views_action);
                    if($has == false){
                        $user_permission = PermissionUser::where('permission_id',$permission->id)->exists();
                        if($user_permission == true){
                            return $this->jsonErrorResponse($data, trans('message.user_has_permission'), 200);
                        }else{
                            $del_permissions = Permission::where('id', $permission->id)->first();
                            $del_permissions->delete();
                        }
                    }
                }
            }
            foreach ($request->views_action as $views_action){
                if(isset($id)){
                    $has = in_array($views_action, $menu_permissions_arr);
                    if($has == true){
                        $menu_permissions = Permission::where('menu_dtl_id', $id)->get();
                        foreach ($menu_permissions as $permissions){
                            if($permissions->display_name == $views_action){
                                $permission = Permission::where('id', $permissions->id)->first();
                            }
                        }
                    }else{
                        $permission = new Permission();
                        $permission->id = Utilities::uuid();
                    }
                }else{
                    $permission = new Permission();
                    $permission->id = Utilities::uuid();
                }
                $permission->menu_id = $menu->menu_id;
                $permission->menu_dtl_id = $menu->menu_dtl_id;
                $permission->name = $menu->menu_dtl_id.'-'.$views_action;
                $permission->display_name = $views_action;
                $permission->description = ucwords($menu->menu_dtl_name).' '.ucwords($views_action);
                $permission->save();
            }

            //clear menu from cache
            Cache::forget('menu');
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
            $menu = TblSoftMenuDtl::where('menu_dtl_id',$id)->first();
            $menu->delete();

            //clear menu from cache
            Cache::forget('menu');
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
