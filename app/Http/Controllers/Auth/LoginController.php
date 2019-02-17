<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

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
    protected $redirectTo = '/home';
    protected $username = 'phone';
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }



    /* ============ API Login Method ============ */
    public function ApiLogin(Request $request)
    {
        $phone = '00966'.$request->phone;
        $request->merge(['phone' => $phone]);
        
        $this->validateLogin($request);

        if ($this->attemptLogin($request)) {
            $user = $this->guard()->user();
            $user->UpdateApiToken();

            return response()->json([
                'status_code' => 200,
                'message' => 'Request process succeed.',
                'data' => $user->toArray(),
            ]);
        }

        return $this->sendFailedLoginResponse($request);
    }

    public function ApiLogout(Request $re)
    {
        $user = $this->guard('api')->user();
        if($user)
        { 
            $user->save();
        }
        return response()->json([
            'data' => 'user logged out'
        ],200);
    }


}
