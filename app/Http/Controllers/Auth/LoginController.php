<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Middleware\CheckBlockedStatusUser;
use App\Http\Middleware\VerifyGoogleRecaptcha;
use App\Providers\RouteServiceProvider;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

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
        $this->middleware(VerifyGoogleRecaptcha::class);
    }

    /**
     * Show the application's login form.
     *
     * @return \Illuminate\View\View
     */
    public function showLoginForm(Request $request)
    {

        if (!empty(Cookie::get('token'))) {
            $token = Cookie::get('token');
            if (isset($token) && !empty($token) && $token != '' && $token != NULL) {
                $get_user_id_db = DB::table('sessions')
                    ->select('user_id')
                    ->where('token', '=', $token)
                    ->get();
                if (isset($get_user_id_db[0]->user_id) && !empty($get_user_id_db[0]->user_id)) {
                    if (Hash::check($get_user_id_db[0]->user_id, $token)) {
                        Auth::loginUsingId($get_user_id_db[0]->user_id);
                        $this->add_log('Login by cookies', $request->ip(), 0);
                        //generate new cookie
                        $this->Generate_cookie($token);
                        return redirect(route('tasks.index'));
                    }
                }
            }
        }
        return view('auth.login');
    }

    protected function loggedOut(Request $request)
    {

        DB::table('sessions')
            ->where('token', '=', Cookie::get('token'))
            ->delete();

        Cookie::queue(Cookie::forget('token'));

        return redirect('/app');
    }

    public function login(Request $request)
    {

        $get_status = DB::table('users')
            ->select('blocked')
            ->where('email', $request->email)
            ->get();

        if (isset($get_status[0]->blocked)) {
            if ($get_status[0]->blocked === 0) {
                $this->validateLogin($request);

                // If the class is using the ThrottlesLogins trait, we can automatically throttle
                // the login attempts for this application. We'll key this by the username and
                // the IP address of the client making these requests into this application.
                if (method_exists($this, 'hasTooManyLoginAttempts') &&
                    $this->hasTooManyLoginAttempts($request)) {
                    $this->fireLockoutEvent($request);

                    return $this->sendLockoutResponse($request);
                }

                if ($this->attemptLogin($request)) {
                    if ($request->hasSession()) {
                        $request->session()->put('auth.password_confirmed_at', time());
                    }

                    $this->add_log('Login', $request->ip(), 0);

                    $lang = DB::table('users_settings')
                        ->select('language')
                        ->where('user_id', '=', Auth::id())
                        ->get();

                    if ($lang[0]) {
                        session()->put('locale', $lang[0]->language);
                    };

                    //generate new cookie
                    $this->Generate_cookie();

                    return $this->sendLoginResponse($request);
                }

                // If the login attempt was unsuccessful we will increment the number of attempts
                // to login and redirect the user back to the login form. Of course, when this
                // user surpasses their maximum number of attempts they will get locked out.
                $this->incrementLoginAttempts($request);
                $this->add_log('AttempLogin', $request->ip(), 0);

                return $this->sendFailedLoginResponse($request);
            } else {
                $this->add_log('AttempLogin', $request->ip(), 0);
                return redirect(route('login'));
            };
        } else {
            $this->add_log('AttempLogin', $request->ip(), 0);
            return redirect(route('login'));
        };
    }


}
