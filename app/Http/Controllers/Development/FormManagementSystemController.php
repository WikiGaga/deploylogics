<?php

namespace App\Http\Controllers\Development;

use App\Http\Controllers\Controller;
use App\Library\Utilities;
use App\Models\Permission;
use App\Models\PermissionHeading;
use App\Models\PermissionUser;
use App\Models\Soft\TblSoftMenu;
use App\Models\Soft\TblSoftMenuDtl;
use App\Models\Stg\TblStagingFunc;
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

class FormManagementSystemController extends Controller
{
    public static $page_title = 'Form Management System';
    public static $redirect_url = '';
    public static $menu_dtl_id = '153';
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
        $data['perPrefix'] = self::$menu_dtl_id.'-1';
        if(isset($id)){
            if(TblSoftMenuDtl::where('menu_dtl_id','LIKE',$id)->exists()){
                $data['page_data'] = array_merge($data['page_data'], Utilities::editForm());
                $data['permission'][] = $data['perPrefix'].'-edit';
                $data['permission'][] = $data['perPrefix'].'-back';
                $data['permission'][] = $data['perPrefix'].'-forward';
                $data['id'] = $id;
                $data['current'] = TblSoftMenuDtl::where('menu_dtl_id','LIKE',$id)->first();
                $data['permissions'] = Permission::where('menu_dtl_id',$id)->get();

            }else{
                abort('404');
            }
        }else{
            $data['permission'] = $data['perPrefix'].'-create';
            $data['page_data'] = array_merge($data['page_data'], Utilities::newForm());
            $data['page_data']['action'] = '';
        }

        $data['menu_dtl_lists'] = TblSoftMenuDtl::orderBy('menu_dtl_name')->get();

        $data['users'] = User::where('user_type','erp')->where('user_entry_status',1)->where(Utilities::currentBC())->orderByRaw('upper(name) asc')->get();


     //   dd($data['permissions']->toARray());
        return view('development.form_management_system.form',compact('data'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $id = null)
    {
      //  dump($request->toArray());
        $data = [];
        DB::beginTransaction();
        try{
            $all_permissions_allow = ($request->has('permissions') && $request->filled('permissions'))?$request->permissions:[];
            if(isset($request->permission)){
                foreach ($request->permission as $u=>$pers){
                    foreach ($pers as $per){
                        foreach ($per as $pe){
                            $all_permissions_allow[$u][] = $pe;
                        }
                    }
                }
            }
            $get_permission = Permission::where('menu_dtl_id',$request->menu_dtl_id)->pluck('id')->toArray();
            $users = User::where('user_type','erp')->where('user_entry_status',1)->where(Utilities::currentBC())->orderByRaw('upper(name) asc')->get('id');
            foreach ($users as $user){
                $user = User::where('id',$user->id)->first();
                $user->detachPermissions($get_permission);
            }
            foreach ($all_permissions_allow as $user_id=>$pers){
                $user = User::where('id',$user_id)->first();
                $user->attachPermissions($pers);
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

        return $this->jsonSuccessResponse($data, 'Successfully', 200);
    }
    public function corePermission($id){
        /*
         * 1 = 1   dev
         * */
        $get_permission = [
            17224121041323,
            10675721041323,
            55125221041323,
            14310621140613,
            22827121140613,
            90228121140613,
            17223521140613,
            22424121140613,
            32137421140613,
            24819621140613,
            16942121140613,
            68792921140613,
            10012221140613,
            25473921140613
        ];
        $users = [1,2,3,81,10425321290929,12218221160957,12617021161007,18731621080411,19206221140943,19219821200856,20422821290939,23551121151410,27236121151430];
        foreach ($users as $user){
            $admin = User::where('id',$user)->first();
            $admin->detachPermissions($get_permission);
        }
        return "OK";
        if($id == 1){
            if(Auth::user()->id == 81){
                $get_permission = PermissionUser::where('user_id',81)->pluck('permission_id')->toArray();
                $admin = User::where('id',81)->first();
                $admin->detachPermissions($get_permission);

                $get_permission = Permission::where('id','<>',1)->where('id','<>',2)->pluck('id')->toArray();
                $admin->attachPermissions($get_permission);
            }
            if(Auth::user()->id == 19206221140943 || Auth::user()->id == 12617021161007){
                $get_permission = [2105290011,
                    2105290012,
                    2105290013,
                    2105290014,
                    2105290015,
                ];
                $admin = User::where('id',Auth::user()->id)->first();
                $admin->detachPermissions($get_permission);
                $admin->attachPermissions($get_permission);
            }


            return 'Done';
        }else{
            abort('404');
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
