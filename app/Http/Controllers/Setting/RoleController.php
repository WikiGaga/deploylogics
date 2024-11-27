<?php

namespace App\Http\Controllers\Setting;

use App\Http\Controllers\Controller;
use App\Library\Utilities;
use App\Models\Permission;
use App\Models\PermissionHeading;
use App\Models\PermissionRole;
use App\Models\PermissionUser;
use App\Models\Role;
use App\Models\TblSoftMenu;
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

class RoleController extends Controller
{
    public static $page_title = 'Role';
    public static $redirect_url = 'role';
    public static $menu_dtl_id = '178';
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
        /*$user = User::where('id',22192121310637)->first();
        $admin = Role::where('id',31141621051500)->first();
        $user->detachRole($admin);
        $user->attachRole($admin);
        $get_permission = PermissionRole::where('role_id',$id)->pluck('permission_id')->toArray();
        foreach ($get_permission as $role_permission){
            $user->detachPermission($role_permission);
        }
        $user->syncPermissions($get_permission); 
        //dd($get_permission);
      //  $user->syncPermissions($get_permission);
      //  dd($user->allPermissions()->toArray());*/
        $data['page_data'] = [];
        $data['page_data']['title'] = self::$page_title;
        $data['page_data']['create'] = '/'.self::$redirect_url.$this->prefixCreatePage;
        $data['page_data']['path_index'] = $this->prefixIndexPage.self::$redirect_url;;
        if(isset($id)){
            if(Role::where('id','LIKE',$id)->exists()){
                $data['page_data'] = array_merge($data['page_data'], Utilities::editForm());
                $data['permission'] = self::$menu_dtl_id.'-edit';
                $data['id'] = $id;
                $data['role'] = Role::where('id',$id)->first();
                $data['current'] = [];
                $get_permission = PermissionRole::where('role_id',$id)->get();
                foreach ($get_permission as $user_permission){
                    array_push($data['current'] ,$user_permission->permission_id);
                }
            }else{
                abort('404');
            }
        }else{
            $data['permission'] = self::$menu_dtl_id.'-create';
            $data['page_data'] = array_merge($data['page_data'], Utilities::newForm());
        }

        $data['permission_head'] = PermissionHeading::get();
        $data['modules'] = TblSoftMenu::where('menu_id','!=' , 0)
            ->with('children')->where(Utilities::currentBC())->orderBy('menu_sorting')->get();

        $userChangePass = Permission::where('display_name','change_password')->where('menu_dtl_id',35)->first();
        $data['custom_modules'] = [];
        if(!empty($userChangePass)) {
            // custom module
            $checked = false;
            if (isset($get_permission)) {
                $collect = collect($get_permission);
                $ChangePassChecked = $collect->where('permission_id', $userChangePass->id)
                    ->where('role_id', $id)->first();
                $userChangePassChecked = ($ChangePassChecked == null) ? false : true;
            }
            $data['custom_modules'] = [
                [
                    'title' => 'User Change Password',
                    'id' => $userChangePass->id,
                    'checked' => isset($userChangePassChecked) ? $userChangePassChecked : $checked,
                ],
            ];
        }
        //dd($data);
        return view('setting.role.form',compact('data'));
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
            'name' => ['required','max:100',Rule::unique('roles')->ignore($id),],
            'd_name' => 'required|max:100'
        ]);
        if ($validator->fails()) {
            $data['validator_errors'] = $validator->errors();
            return $this->jsonErrorResponse($data, trans('message.required_fields'), 200);
        }
        DB::beginTransaction();
        try{
            if(isset($id)){
                $role = Role::where('id',$id)->first();
            }else{
                $role = new Role();
                $role->id = Utilities::uuid();
                $role->name = $this->strLower($request->name);
            }
            $role->display_name = $request->d_name;
            $role->description = $request->description;
            $role->business_id = auth()->user()->business_id;
            $role->company_id = auth()->user()->company_id;
            $role->branch_id = auth()->user()->branch_id;
            $role->role_user_id = auth()->user()->id;
            $role->save();

            if(isset($id)){
                $get_permission = PermissionRole::where('role_id',$role->id)->get();
                foreach ($get_permission as $role_permission){
                    $role->detachPermission($role_permission->permission_id);
                }
                $permissions = ($request->has('permissions') && $request->filled('permissions'))?$request->permissions:[];
                $role->syncPermissions($permissions);
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

        $data['id'] = $role->id;
        if(isset($id)){
            $data = array_merge($data, Utilities::returnJsonEditForm());
            $data['redirect'] = '/'.self::$redirect_url.$this->prefixCreatePage."/".$data['id'];
            return $this->jsonSuccessResponse($data, trans('message.update'), 200);
        }else{
            $data = array_merge($data, Utilities::returnJsonNewForm());
            $data['redirect'] = '/'.self::$redirect_url.$this->prefixCreatePage."/".$data['id'];
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
        //
    }
}
