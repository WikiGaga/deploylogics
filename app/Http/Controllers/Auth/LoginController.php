<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Library\Utilities;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
// db and Validator
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;
use Validator;
class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }
    protected function validateLogin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            $this->username() => 'required|string',
            'password' => 'required|string',
        ]);
        if ($validator->fails()) {
            $data['validator_errors'] = $validator->errors();
            return $this->jsonErrorResponse($data, trans('message.required_fields'), 422);
        }

    }
    protected function authenticated(Request $request, $user)
    {
       // Auth::logoutOtherDevices($request->password);
        /*$userToLogout = User::find($user->id);
        Auth::setUser($userToLogout);
        Auth::logout();*/

        /*$data = [];
        if($user->last_session ==  1)
        {
            $this->guard()->logout();
            $request->session()->invalidate();
            session()->regenerate();
            $data['token'] = csrf_token();
            return $this->jsonErrorResponse($data, trans('message.already_login'), 200);
        }else{
            Utilities::userLoggedSession(auth()->user()->id,1);
        }*/
    }


    public function login(Request $request)
    {
        $this->validateLogin($request);

        if ($request->device == 'mobile') {
            return $this->jsonErrorResponse([], 'you can not login', 200);
        }

        // If the class is using the ThrottlesLogins trait, we can automatically throttle
        // the login attempts for this application. We'll key this by the username and
        // the IP address of the client making these requests into this application.
        if (method_exists($this, 'hasTooManyLoginAttempts') &&
            $this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

            return $this->sendLockoutResponse($request);
        }

        if ($this->attemptLogin($request)) {
            return $this->sendLoginResponse($request);
        }

        // If the login attempt was unsuccessful we will increment the number of attempts
        // to login and redirect the user back to the login form. Of course, when this
        // user surpasses their maximum number of attempts they will get locked out.
        $this->incrementLoginAttempts($request);

        return $this->sendFailedLoginResponse($request);
    }


    /**
     * Send the response after the user was authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    protected function sendLoginResponse(Request $request)
    {
        $user = $this->guard()->user();
        $wanAddress = 'erp.risen.com.pk';
        if (str_contains($request->url(), $wanAddress) && ($user && $user->ip_address_apply == 0)) {
            Auth::logout();
            return $this->jsonErrorResponse([], 'Access not allowed', 403);
        }

        $request->session()->regenerate();

        $this->clearLoginAttempts($request);

        if ($response = $this->authenticated($request, $this->guard()->user())) {
            return $response;
        }

        return $request->wantsJson()
                    ? new Response('', 204)
                    : $this->jsonSuccessResponse(['status'=>'success'], 'Successfully LoggedIn' , 200);
    }


    public function logout(Request $request)
    {
        
        //Utilities::userLoggedSession(auth()->user()->id,0);
        $this->guard()->logout();
        $request->session()->invalidate();
        return redirect('/');
    }

     /**
     * Get the guard to be used during authentication.
     *
     * @return \Illuminate\Contracts\Auth\StatefulGuard
     */
    protected function guard()
    {
        return Auth::guard();
    }

}
