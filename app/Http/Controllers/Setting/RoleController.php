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
use App\Models\EmployeeRole;
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
        $data['dash_permission'] = Permission::where('name','dash-view')->first();
        $data['flow_dash_permission'] = Permission::where('name','flow-dash-view')->first();
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

        $data['vendor_modules'] = [
            'food', 'order', 'kitchen_orders', 'restaurant_setup', 'addon', 'wallet', 'employee',
            'my_shop', 'chat', 'campaign', 'reviews', 'pos', 'subscription', 'coupon', 'report',
            'custom_role', 'options_list', 'shift_session', 'printer_settings'
        ];
        $data['vendor_module_labels'] = [
            'food' => 'Food',
            'order' => 'Order',
            'kitchen_orders' => 'Kitchen Orders',
            'restaurant_setup' => 'Restaurant Setup',
            'addon' => 'Addon',
            'wallet' => 'Wallet',
            'employee' => 'Employee',
            'my_shop' => 'My Shop',
            'chat' => 'Chat',
            'campaign' => 'Campaign',
            'reviews' => 'Reviews',
            'pos' => 'POS',
            'subscription' => 'Subscription',
            'coupon' => 'Coupon',
            'report' => 'Report',
            'custom_role' => 'Custom Role',
            'options_list' => 'Options List',
            'shift_session' => 'Shift Session',
            'printer_settings' => 'Printer Settings',
        ];

        $data['vendor_modules_selected'] = [];
        if(isset($id)){
            $employeeRole = EmployeeRole::where('id', $id)
                ->whereNull('restaurant_id')
                ->first();
            if($employeeRole && !empty($employeeRole->modules)){
                $data['vendor_modules_selected'] = json_decode($employeeRole->modules, true) ?: [];
            }
        }

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
            }
            // Sync permissions (works for both create and edit)
            // $permissions = ($request->has('permissions') && $request->filled('permissions'))?$request->permissions:[];
            // $role->syncPermissions($permissions);
            // Sync permissions (works for both create and edit)
            $permissions = ($request->has('permissions') && $request->filled('permissions')) ? (array)$request->permissions : [];

            // Crucial Fix: Filter out empty strings, null values, or zeros that cause ORA-01400
            $permissions = array_filter($permissions, function($value) {
                return $value !== null && $value !== '';
            });

            $role->syncPermissions($permissions);

            if($request->has('vendor_modules') && $request->filled('vendor_modules')){
                $vendorModules = $request->vendor_modules;
                $allowedModules = [
                    'food', 'order', 'kitchen_orders', 'restaurant_setup', 'addon', 'wallet', 'employee',
                    'my_shop', 'chat', 'campaign', 'reviews', 'pos', 'subscription', 'coupon', 'report',
                    'custom_role', 'options_list', 'shift_session', 'printer_settings'
                ];
                $vendorModules = array_intersect($vendorModules, $allowedModules);

                $employeeRole = EmployeeRole::where('id', $role->id)
                    ->whereNull('restaurant_id')
                    ->first();

                if($employeeRole){
                    $employeeRole->name = $role->display_name;
                    $employeeRole->modules = json_encode($vendorModules);
                    $employeeRole->status = 1;
                    $employeeRole->save();
                } else {
                    $employeeRole = new EmployeeRole();
                    $employeeRole->id = $role->id;
                    $employeeRole->name = $role->display_name;
                    $employeeRole->modules = json_encode($vendorModules);
                    $employeeRole->status = 1;
                    $employeeRole->restaurant_id = null;
                    $employeeRole->save();
                }
            } else {
                $employeeRole = EmployeeRole::where('id', $role->id)
                    ->whereNull('restaurant_id')
                    ->first();

                if($employeeRole){
                    $employeeRole->name = $role->display_name;
                    $employeeRole->modules = json_encode([]);
                    $employeeRole->status = 1;
                    $employeeRole->save();
                } else {
                    $employeeRole = new EmployeeRole();
                    $employeeRole->id = $role->id;
                    $employeeRole->name = $role->display_name;
                    $employeeRole->modules = json_encode([]);
                    $employeeRole->status = 1;
                    $employeeRole->restaurant_id = null;
                    $employeeRole->save();
                }
            }

            if(isset($id)){
                $usersWithRole = User::where('employee_role_id', $role->id)
                    ->where('user_entry_status', 1)
                    ->where(Utilities::currentBC())
                    ->get();
                foreach ($usersWithRole as $user){
                    $user->syncPermissions($permissions);
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
