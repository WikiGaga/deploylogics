<?php

namespace App\Http\Controllers\Development;

use App\Http\Controllers\Controller;
use App\Library\Utilities;
use App\Models\Module;
use App\Models\Permission;
use App\Models\PermissionHeading;
use App\Models\PermissionUser;
use App\Models\TblSoftMenu;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

// db and Validator
use Illuminate\Validation\Rule;
use Validator;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Maatwebsite\Excel\Concerns\ToArray;

class UserManagementSystemController extends Controller
{
    public static $page_title = 'User Management System';
    public static $redirect_url = '';
    public static $menu_dtl_id = '59';
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
        $id2 = null;
        if($id != null)
        {
            $array = explode('&',$id);
            
            if($array[0] > 0)
            {
                $id = $array[0];
                $id2 = $array[1];
                $type_id = $array[2];
                $startQuery = User::where('id','LIKE',$id)->where('user_entry_status',1)->where(Utilities::currentBC())->exists();
                $permissions = PermissionUser::where('user_id',$id)->get();
            }
            
            if($array[1] > 0)
            {
                $id = $array[0];
                $id2 = $array[1];
                $type_id = $array[2];
                $startQuery = User::where('id','LIKE',$id2)->where('user_entry_status',1)->where(Utilities::currentBC())->exists();
                $permissions = PermissionUser::where('user_id',$id2)->get();
            }
        }

        $data['page_data'] = [];
        $data['page_data']['title'] = self::$page_title;
       // $data['page_data']['path_index'] = $this->prefixIndexPage.self::$redirect_url;;
        if(isset($id)){
            if($startQuery)
            {
                /*if(User::where('id','LIKE',$id)->where('user_entry_status',1)->where(Utilities::currentBC())->exists())
                {*/
                $data['page_data'] = array_merge($data['page_data'], Utilities::editForm());
                $data['permission'] = self::$menu_dtl_id.'-edit';
                $data['id'] = $id;
                $data['id2'] = $id2;
                $data['type_id'] = $type_id;
                $data['current'] = [];
                $get_permission = $permissions;
                foreach ($get_permission as $user_permission){
                    array_push($data['current'] ,$user_permission->permission_id);
                }
            }else{
                abort('404');
            }
        }else{
            $data['permission'] = self::$menu_dtl_id.'-create';
            $data['page_data'] = array_merge($data['page_data'], Utilities::newForm());
            $data['page_data']['action'] = '';
        }
        $data['users'] = User::where('user_entry_status',1)->where(Utilities::currentBC())->orderByRaw('upper(name) asc')->get();

        $data['permission_head'] = PermissionHeading::get();
        $data['modules'] = TblSoftMenu::where('menu_id','!=' , 0)
            ->with('children')->where(Utilities::currentBC())->orderBy('menu_sorting')->get();

        
        $complete_module = Permission::where('display_name','complete_module')->where('menu_dtl_id',309)->first();
        $close_module = Permission::where('display_name','close_module')->where('menu_dtl_id',309)->first();
        $un_post_module = Permission::where('display_name','un_post_module')->where('menu_dtl_id',309)->first();
        $userChangePass = Permission::where('display_name','change_password')->where('menu_dtl_id',35)->first();
        $data['custom_modules'] = [];
        if(!empty($userChangePass) || !empty($complete_module) || !empty($close_module) || !empty($un_post_module)){
            // custom module
            $checked = false;
            $checked_complete = false;
            $checked_close = false;
            $checked_un_post = false;

            if(isset($get_permission)){
                $collect =  collect($get_permission);

                $AuditCompleteChecked = $collect->where('permission_id',$complete_module->id)
                    ->where('user_id',$id)->first();
                $userCompleteChecked = ($AuditCompleteChecked == null)?false:true;

                $AuditCloseChecked = $collect->where('permission_id',$close_module->id)
                    ->where('user_id',$id)->first();
                $userCloseChecked = ($AuditCloseChecked == null)?false:true;

                $AuditunpostChecked = $collect->where('permission_id',$un_post_module->id)
                    ->where('user_id',$id)->first();
                $userunpostChecked = ($AuditunpostChecked == null)?false:true;


                $ChangePassChecked = $collect->where('permission_id',$userChangePass->id)
                    ->where('user_id',$id)->first();
                $userChangePassChecked = ($ChangePassChecked == null)?false:true;
            }
            $data['custom_modules'] = [
                [
                    'title' => 'User Change Password',
                    'id' => $userChangePass->id,
                    'checked' => isset($userChangePassChecked)?$userChangePassChecked:$checked,
                ],
                [
                    'title' => 'Complete Audit',
                    'id' => $complete_module->id,
                    'checked' => isset($userCompleteChecked)?$userCompleteChecked:$checked_complete,
                ],
                [
                    'title' => 'Close Audit',
                    'id' => $close_module->id,
                    'checked' => isset($userCloseChecked)?$userCloseChecked:$checked_close,
                ],
                [
                    'title' => 'Un-Post Audit',
                    'id' => $un_post_module->id,
                    'checked' => isset($userunpostChecked)?$userunpostChecked:$checked_un_post,
                ],
            ];
        }
        return view('development.user_management_system.form',compact('data'));
    }
    public function corePermission(){
        /*
         * 1988 = 81   umar
         * 2 = 80   Zaryab
         * 3 = 91   dev
         * 4 = 19138121131418 ghazan
         * */
        try{
            $user = Auth::user()->id;
            if($user == 80 || $user == 81 || $user == 91 || $user == 19138121131418){
                $get_permission = Permission::where('menu_dtl_id',self::$menu_dtl_id)->pluck('id')->toArray();
                $admin = User::where('id',$user)->first();
                $admin->detachPermissions($get_permission);
                $admin->attachPermissions($get_permission);
                return ("done");
            }else{
                return ("something wrong error.");
            }

        }catch (Exception $e){
            return  ("DB Error: ").$e->getLine();
        }
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
        DB::beginTransaction();
        try{

            $user = User::where('id',$request->user_id)->where('user_entry_status',1)->where(Utilities::currentBC())->first();
            // $get_permission = PermissionUser::where('user_id',$request->user_id)->get();
            /*foreach ($get_permission as $user_permission){
                $user->detachPermission($user_permission->permission_id);
            }*/
            $permissions = ($request->has('permissions') && $request->filled('permissions'))?$request->permissions:[];
            $permissions = array_filter($permissions);
            $permissions = array_slice($permissions,0);
            $user->syncPermissions($permissions);

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

        return $this->jsonSuccessResponse($data, 'Successfully', 200);
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
