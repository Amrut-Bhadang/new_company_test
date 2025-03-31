<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\User;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
    public function showLoginForm()
    {
        return view('auth.login');
    }
    public function login(Request $request)
    {
        $input =  $request->all();
        $message = [
            'email.required' => __("backend.email_required"),
            'email.email' => __("backend.email_email"),
            'password.required' => __("backend.password_required"),
            'password.min' => __("backend.password_min"),
        ];
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ], $message);
        // If the class is using the ThrottlesLogins trait, we can automatically throttle
        // the login attempts for this application. We'll key this by the username and
        // the IP address of the client making these requests into this application.

        if (
            method_exists($this, 'hasTooManyLoginAttempts') &&
            $this->hasTooManyLoginAttempts($request)
        ) {
            $this->fireLockoutEvent($request);

            return $this->sendLockoutResponse($request);
        }

        if ($this->attemptLogin($request)) {
            if(isset($input["remember_me"]))
            {
                // dd('remember_me');
            $hour = time() + 3600 * 24 * 30;
            setcookie('admin_email', $input['email'], $hour);
            setcookie('admin_password', $input['password'], $hour);
            setcookie('admin_remember_me', $input['remember_me'], $hour);
            }else{
                // dd('remember_me no', $input);
                setcookie("admin_email","");
                setcookie("admin_password","");
                setcookie("admin_remember_me","");
            }
            return $this->sendLoginResponse($request);
        }

        // If the login attempt was unsuccessful we will increment the number of attempts
        // to login and redirect the user back to the login form. Of course, when this
        // user surpasses their maximum number of attempts they will get locked out.
        $this->incrementLoginAttempts($request);

        return $this->sendFailedLoginResponse($request);
    }
    protected function sendLoginResponse(Request $request)
    {
        $request->session()->regenerate();

        $this->clearLoginAttempts($request);

        if ($response = $this->authenticated($request, $this->guard()->user())) {
            return $response;
        }
        return redirect()->route('admin.home');
        // return $request->wantsJson()
        //             ? new JsonResponse([], 204)
        //             : redirect()->intended($this->redirectPath());
    }
    public function logout(Request $request)
    {
        $this->guard()->logout();
        return redirect()->route('admin.login');
    }
    protected function authenticated(Request $request, $user)
    {
        if ($user->status == 1) {
            if($user->type == 0 || $user->type == 1){
                return redirect()->route('admin.home');
            }else{
                Auth::logout();
                return redirect()->route('admin.login')->with('error_msg', __("backend.Invaild_user"));
            }
        } else {
            Auth::logout();
            return redirect()->route('admin.login')->with('error_msg', __("backend.account_deactivate_message"));
        }
    }
}
