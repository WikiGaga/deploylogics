<?php

namespace App\Http\Controllers\Setting;

use App\Http\Controllers\Controller;
use App\Library\Utilities;
use App\Models\User;
use Illuminate\Http\Request;

// db and Validator
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Validator;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
class PasswordController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public static $page_title = 'Password Change';
    public static $redirect_url = '';

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
        $data['page_data']['path_index'] = $this->prefixIndexPage.self::$redirect_url;;
        $data['page_data'] = array_merge($data['page_data'], Utilities::newForm());
        return view('setting.password.form',compact('data'));
    }

    public function createPos($id = null)
    {
        $data['page_data'] = [];
        $data['page_data']['title'] = self::$page_title;
        $data['page_data']['path_index'] = $this->prefixIndexPage.self::$redirect_url;;
        $data['page_data'] = array_merge($data['page_data'], Utilities::newForm());
        return view('setting.pos_password.form',compact('data'));
    }

    public function createChangePass($id)
    {
       // dd($id);
        if(isset($id)){
            $data['page_data'] = [];
            $data['page_data']['title'] = 'Change Password';
            $data['page_data']['path_index'] = $this->prefixIndexPage.self::$redirect_url;;
            $data['page_data'] = array_merge($data['page_data'], Utilities::editForm());
            $data['user'] = User::where('id',$id)->where(Utilities::currentBC())->first();
           // dd($data['user']);
            if(empty($data['user'])){
                abort('404');
            }
            return view('setting.change_password.form',compact('data'));

        }else{
            abort('404');
        }
    }
    public function newChangecreate($id = null)
    {
        $data['page_data'] = [];
        $data['page_data']['title'] = self::$page_title;
        $data['page_data']['path_index'] = "/new_change_password/form";//$this->prefixIndexPage.self::$redirect_url;
        $data['page_data'] = array_merge($data['page_data'], Utilities::newForm());
        $data['users'] = User::where('user_entry_status',1)->where(Utilities::currentBC())->orderByRaw('upper(name) asc')->get();

        return view('setting.new_change_password.form',compact('data'));
    }
    public function newPosChangecreate($id = null)
    {
        $data['page_data'] = [];
        $data['page_data']['title'] = 'POS Change Password';
        $data['page_data']['path_index'] = "/new_pos_change_password/form";//$this->prefixIndexPage.self::$redirect_url;
        $data['page_data'] = array_merge($data['page_data'], Utilities::newForm());
        $data['users'] = User::where('user_entry_status',1)->where(Utilities::currentBC())->orderByRaw('upper(name) asc')->get();

        return view('setting.new_pos_change_password.form',compact('data'));
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
            'old_password' => 'required',
            'new_password' => 'required',
            'conform_password' => 'required|same:new_password'
        ]);
        if ($validator->fails()) {
            $data['validator_errors'] = $validator->errors();
            return $this->jsonErrorResponse($data, trans('message.required_fields'), 422);
        }
        DB::beginTransaction();
        try{
            $user = User::where('id',$id)->where('user_type','erp')->where('user_entry_status',1)->where(Utilities::currentBC())->first();
            if(!Hash::check($request->old_password,$user->password)){
                return $this->returnjsonerror("Old Password Not Correct ",201);
            }
            $user->password = Hash::make($request->new_password);
            $user->save();

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
            return $this->jsonSuccessResponse($data, trans('message.create'), 200);
        }
    }

    public function storePos(Request $request, $id = null)
    {
        $data = [];
        $validator = Validator::make($request->all(), [
            'new_password' => 'required',
            'conform_password' => 'required|same:new_password'
        ]);
        if ($validator->fails()) {
            $data['validator_errors'] = $validator->errors();
            return $this->jsonErrorResponse($data, trans('message.required_fields'), 422);
        }
        DB::beginTransaction();
        try{
            $user = User::where('id',$id)->where('user_type','erp')->where('user_entry_status',1)->where(Utilities::currentBC())->first();
            $user->password_pos = $request->new_password;
            $user->save();

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
            return $this->jsonSuccessResponse($data, trans('message.create'), 200);
        }
    }

    public function storeChangePass(Request $request, $id = null)
    {
       // dd($request->toArray());
        $data = [];
        if(!empty($request->new_password)) {
            $validator = Validator::make($request->all(), [
                'new_password' => 'required',
                'conform_password' => 'required|same:new_password'
            ]);
        }
        if(!empty($request->new_password_pos)) {
            $validator = Validator::make($request->all(), [
                'new_password_pos' => 'required',
                'conform_password_pos' => 'required|same:new_password_pos'
            ]);
        }
        if(!empty($request->new_password) && !empty($request->new_password_pos)){
            return $this->jsonErrorResponse($data, trans('message.required_fields'), 422);
        }
        if ($validator->fails()) {
            $data['validator_errors'] = $validator->errors();
            return $this->jsonErrorResponse($data, trans('message.required_fields'), 422);
        }
        DB::beginTransaction();
        try{

            $user = User::where('id',$id)->where(Utilities::currentBC())->first();

            if(!empty($request->new_password)){
                $user->password = Hash::make($request->new_password);
            }
            if(!empty($request->new_password_pos)){
                $user->password_pos = $request->new_password_pos;
            }
            $user->save();

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
            $data['change_pass'] = 'change_pass';
            $data['redirect'] = '/listing/user_account';
            return $this->jsonSuccessResponse($data, trans('message.update'), 200);
        }else{
            $data = array_merge($data, Utilities::returnJsonNewForm());
            return $this->jsonSuccessResponse($data, trans('message.create'), 200);
        }
    }

    public function newChangestore(Request $request, $id = null)
    {
        $data = [];
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'new_password' => 'required',
            'conform_password' => 'required|same:new_password'
        ]);
        if ($validator->fails()) {
            $data['validator_errors'] = $validator->errors();
            return $this->jsonErrorResponse($data, trans('message.required_fields'), 422);
        }
        DB::beginTransaction();
        try{
            $user = User::where('id',$request->user_id)->where('user_type','erp')->where('user_entry_status',1)->where(Utilities::currentBC())->first();
            $user->password = Hash::make($request->new_password);
            $user->save();

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
        /*if(isset($id)){*/
            $data = array_merge($data, Utilities::returnJsonEditForm());
            $data['change_pass'] = 'change_pass';
            $data['redirect'] = "/new_change_password/form";
            return $this->jsonSuccessResponse($data, trans('message.update'), 200);
        /*}else{
            $data = array_merge($data, Utilities::returnJsonNewForm());
            return $this->jsonSuccessResponse($data, trans('message.create'), 200);
        }*/
    }

    
    public function newPosChangestore(Request $request, $id = null)
    {
        $data = [];
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'new_password' => 'required',
            'conform_password' => 'required|same:new_password'
        ]);
        if ($validator->fails()) {
            $data['validator_errors'] = $validator->errors();
            return $this->jsonErrorResponse($data, trans('message.required_fields'), 422);
        }
        DB::beginTransaction();
        try{
            $user = User::where('id',$request->user_id)->where('user_type','pos')->where('user_entry_status',1)->where(Utilities::currentBC())->first();
            $user->password_pos = $request->new_password;
            $user->update_id = Utilities::uuid();
            $user->save();

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
        /*if(isset($id)){*/
            $data = array_merge($data, Utilities::returnJsonEditForm());
            $data['redirect'] = "/new_pos_change_password/form";
            return $this->jsonSuccessResponse($data, trans('message.update'), 200);
        /*}else{
            $data = array_merge($data, Utilities::returnJsonNewForm());
            return $this->jsonSuccessResponse($data, trans('message.create'), 200);
        }*/
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
