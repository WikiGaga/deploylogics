<?php

namespace App\Http\Controllers\Api\Auth;

use Validator;
use App\Models\User;
use App\Models\TblSoftBusiness;
use App\Models\TblSoftBranch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ApiController;
use Illuminate\Support\Facades\Hash;
use App\Library\ApiUtilities;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Exception;
use Illuminate\Support\Facades\Session;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;


class LoginController extends ApiController
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('auth:api', ['except' => ['login', 'register','publicConfig']]);
    }


    //company list for login
    public function publicConfig(){
        $data['business_list'] = TblSoftBusiness::select('business_id','business_short_name')->get();
        return $this->ApiJsonSuccessResponse($data,'Public config data');
    }

    /**
     * Get a JWT token via given credentials.
     *
     * @param  \Illuminate\Http\Request  $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request){
        $data = (object)[];
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string',
            'business_id' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return $this->ApiJsonErrorResponse($data,'Field is required');
        }

        if (! $token = auth()->attempt($validator->validated())) {
            return $this->ApiJsonErrorResponse($data,'The given data was invalid');
        }

        $credentials = $request->only('email', 'password','business_id');

        if ($token = $this->guard()->attempt($credentials)) {
            $data =[
                'user_id' => $this->guard()->user()->id,
                'access_token' => $token,
                'token_type' => 'bearer',
                'expires_in' => (int)$this->guard()->factory()->getTTL()
            ];
        }

        if(!empty($data)){
            $data['branch_list']= DB::table('tbl_soft_branch')
                                ->join('tbl_soft_user_branch', 'tbl_soft_branch.branch_id', '=', 'tbl_soft_user_branch.branch_id')
                                ->select('tbl_soft_branch.branch_id as branch_id','branch_short_name')
                                ->where('user_id',$this->guard()->user()->id)
                                ->where('business_id',$request->business_id)
                                ->where('company_id',$request->business_id)
                                ->get();

            $data['total_branches'] = count($data['branch_list']);
        }
        $this->addSession();
        return $this->ApiJsonSuccessResponse($data,'login successfully');
    }

    //attach branch to user
    public function verifyBranch(Request $request)
    {
        $data = (object)[];
        $validator = Validator::make($request->all(), [
            'branch_id' => 'required'
        ]);
        if ($validator->fails()) {
            return $this->ApiJsonErrorResponse($data,'Field is required');
        }
        $getAllBranches = ApiUtilities::getAllBranches();
        $arr = [];
        foreach($getAllBranches as $branch){
            array_push($arr,$branch->branch_id);
        }
        if (!in_array($request->branch_id,$arr)) {
            return $this->ApiJsonErrorResponse($data,'Branch Not Exist in User');
        }

        return $this->ApiJsonSuccessResponse($data,'Branch Verification Successfully Done');
    }

    //private config
    public function privateConfig(){
        $data['config_data'] = Session::get('ApiDataSession');
        return $this->ApiJsonSuccessResponse($data,'Configuration Details');
    }

    //dashboard
    public function dashboard(){
        $data = (object)[];
        return $this->ApiJsonSuccessResponse($data,'empty dashboard');
    }
    /**
     * Get the authenticated User
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        $data['user_data'] = $this->guard()->user();
        return $this->ApiJsonSuccessResponse($data,'user data');
    }


    public function register(Request $request) {

        //dd($request->toArray());

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|between:2,100',
            'email' => 'required|string|email|max:100|unique:users',
            'password' => 'required|string|min:6',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }


        $user = new User();
        $user->id = Utilities::uuid();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->branch_id = 1;
        $user->business_id = 1;
        $user->company_id = 1;
        $user->save();

        return response()->json([
            'message' => 'User successfully registered',
            'user' => $user
        ], 201);
    }



    /**
     * Log the user out (Invalidate the token)
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        $data = [];
        $this->guard()->logout();
        return $this->ApiJsonSuccessResponse($data,'Successfully logged out');
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken($this->guard()->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => $this->guard()->factory()->getTTL() * 60
        ]);
    }

    /**
     * Get the guard to be used during authentication.
     *
     * @return \Illuminate\Contracts\Auth\Guard
     */
    public function guard()
    {
        return Auth::guard('api');
    }

    protected function createNewToken($token){
        return $data = [
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => $this->guard()->factory()->getTTL() * 60,
            'user' => auth()->user()
        ];
    }
}
