<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\ApiController;
use App\Http\Controllers\Controller;
use App\Http\Middleware\CheckBlockedStatusUser;
use App\Providers\RouteServiceProvider;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Crypt;
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
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Show the application's login form.
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

        $this->VerifyRecaptcha($request);

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

                    //Kontrola či máme dvoj faktor
                    $verify = DB::table('mfa_authorization')
                        ->where('user_id', '=', Auth::id())
                        ->count();

                    if ($verify > 0 && !isset($request->mfacode)) {

                        $device_id = DB::table('mfa_authorization')
                            ->select('mfa_authorization.device_id', 'fcm_tokens.fcm_token')
                            ->join('fcm_tokens', 'fcm_tokens.id', '=', 'mfa_authorization.device_id')
                            ->where('mfa_authorization.user_id', '=', Auth::id())
                            ->get();

                        if ($device_id->count() > 0) {
                            $rand_code = rand(1000, 32766);

                            DB::table('mfa_codes')
                                ->insert([
                                    'user_id' => Auth::id(),
                                    'device_id' => $device_id[0]->device_id,
                                    'code' => $rand_code,
                                    'used' => 0,
                                    'created_at' => Carbon::now()
                                ]);

                            $body = "Kód: " . $rand_code . ". ";
                            $body .= "Overovací kód pre dvojfaktorové prihlásenie. ";

                            $apiController = new ApiController();
                            $status = $apiController->sendNotification("Notificate.me", $body, Auth::id(), $device_id[0]->fcm_token);

                            $this->add_log('TwoFactorCodeSend', $request->ip(), 0);
                            Auth::logout();
                            $request->session()->invalidate();
                            $request->session()->regenerateToken();

                            return view('auth.mfa_confirm', [
                                'email' => $request->email,
                                'password' => $request->password
                            ]);
                        };
                        Auth::logout();
                        $request->session()->invalidate();
                        $request->session()->regenerateToken();
                        return redirect('/login');
                    };

                    if ($verify > 0 && isset($request->mfacode)) {
                        if (!empty($request->mfacode)) {
                            $device_id = DB::table('mfa_authorization')
                                ->select('device_id')
                                ->where('user_id', '=', Auth::id())
                                ->get();

                            if ($device_id->count() > 0) {
                                $verify_token = DB::table('mfa_codes')
                                    ->select('code')
                                    ->where('device_id', '=', $device_id[0]->device_id)
                                    ->where('user_id', '=', Auth::id())
                                    ->where('used', '=', 0)
                                    ->orderByDesc('id')
                                    ->whereDate('created_at', '>', Carbon::now()->subDays(1))
                                    ->limit(1)
                                    ->get();

                                if ($verify_token->count() > 0) {
                                    if ($verify_token[0]->code == $request->mfacode) {
                                        DB::table('mfa_codes')
                                            ->where('device_id', '=', $device_id[0]->device_id)
                                            ->where('user_id', '=', Auth::id())
                                            ->where('code', '=', $request->mfacode)
                                            ->update([
                                                'used' => 1,
                                                'updated_at' => Carbon::now()
                                            ]);
                                        $this->add_log('LoginTwoFactorCode', $request->ip(), 0);
                                    } else {
                                        //Skúsime overiť či to nie je záložný kód
                                        $recovery_words = DB::table('mfa_authorization')
                                            ->select('recovery_codes')
                                            ->where('user_id', '=', Auth::id())
                                            ->get();

                                        if ($recovery_words->count() > 0) {
                                            $recovery_words = explode(',', Crypt::decrypt($recovery_words[0]->recovery_codes));
                                            if (in_array($request->mfacode, $recovery_words)) {
                                                if (($key = array_search(strtoupper($request->mfacode), $recovery_words)) !== FALSE) {
                                                    unset($recovery_words[$key]);
                                                    $recovery_words = Crypt::encrypt(implode(',', $recovery_words));
                                                    DB::table('mfa_authorization')
                                                        ->where('user_id', '=', Auth::id())
                                                        ->update([
                                                            'recovery_codes' => $recovery_words,
                                                            'updated_at' => Carbon::now()
                                                        ]);
                                                    $this->add_log('LoginTwoFactorRecoveryCode', $request->ip(), 0);
                                                } else {
                                                    $this->add_log('WrongTwoFactor', $request->ip(), 0);
                                                    Auth::logout();
                                                    $request->session()->invalidate();
                                                    $request->session()->regenerateToken();
                                                    return redirect('/login');
                                                };
                                            } else {
                                                $this->add_log('WrongTwoFactor', $request->ip(), 0);
                                                Auth::logout();
                                                $request->session()->invalidate();
                                                $request->session()->regenerateToken();
                                                return redirect('/login');
                                            };
                                        } else {
                                            $this->add_log('WrongTwoFactor', $request->ip(), 0);
                                            Auth::logout();
                                            $request->session()->invalidate();
                                            $request->session()->regenerateToken();
                                            return redirect('/login');
                                        };
                                    };
                                } else {
                                    $this->add_log('WrongTwoFactor', $request->ip(), 0);
                                    Auth::logout();
                                    $request->session()->invalidate();
                                    $request->session()->regenerateToken();
                                    return redirect('/login');
                                };
                            } else {
                                $this->add_log('WrongTwoFactor', $request->ip(), 0);
                                Auth::logout();
                                $request->session()->invalidate();
                                $request->session()->regenerateToken();
                                return redirect('/login');
                            };
                        } else {
                            $this->add_log('WrongTwoFactor', $request->ip(), 0);
                            Auth::logout();
                            $request->session()->invalidate();
                            $request->session()->regenerateToken();
                            return redirect('/login');
                        };
                    };


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
