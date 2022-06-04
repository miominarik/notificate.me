<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Middleware\VerifyGoogleRecaptcha;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;

class OauthController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(VerifyGoogleRecaptcha::class);
    }

    /**
     * AppleOath
     *
     * @param mixed $request
     * @return void
     */
    public function AppleOauth(Request $request)
    {
        if (!empty($request->state) && $request->state != '' && !empty($request->code) && $request->code != '') {
            $appleUser = Socialite::driver('apple')->stateless()->user();

            $check = User::where('apple_id', $appleUser->id)->count();

            if ($check) {
                $user = User::where('apple_id', $appleUser->id)->first();
                Auth::login($user);
            } else {
                $check_if_email_exits = User::where('email', $appleUser->email)->count();
                if ($check_if_email_exits == 0) {
                    $user = new User;

                    $user->email = $appleUser->email;
                    $user->password = Hash::make(Str::random(30));
                    $user->apple_id = $appleUser->id;
                    $user->email_verified_at = Carbon::now();
                    $user->created_at = Carbon::now();

                    $user->save();

                    DB::table('users_settings')->insert([
                        'user_id' => $user->id,
                        'created_at' => Carbon::now()
                    ]);

                    DB::table('modules')->insert([
                        'user_id' => $user->id,
                        'created_at' => Carbon::now()
                    ]);

                    Auth::login($user);
                }
            };
        };
        return redirect('/app');
    }

    /**
     * GoogleOauth
     *
     * @param mixed $request
     * @return void
     */
    public function GoogleOauth(Request $request)
    {
        if (!empty($request->query('state')) && $request->query('state') != '' && !empty($request->query('code')) && $request->query('code') != '') {
            $googleUser = Socialite::driver('google')->stateless()->user();

            $check = User::where('google_id', $googleUser->id)->count();

            if ($check) {
                $user = User::where('google_id', $googleUser->id)->first();
                Auth::login($user);
            } else {
                if (Auth::check() == true) {
                    User::where('id', Auth::id())
                        ->update([
                            'google_id' => $googleUser->id,
                            'updated_at' => Carbon::now()
                        ]);
                    return redirect(route('settings.index'));
                } else {
                    $user = new User;

                    $user->email = $googleUser->email;
                    $user->password = Hash::make(Str::random(30));
                    $user->google_id = $googleUser->id;
                    $user->email_verified_at = Carbon::now();
                    $user->created_at = Carbon::now();

                    $user->save();

                    DB::table('users_settings')->insert([
                        'user_id' => $user->id,
                        'created_at' => Carbon::now()
                    ]);

                    DB::table('modules')->insert([
                        'user_id' => $user->id,
                        'created_at' => Carbon::now()
                    ]);

                    Auth::login($user);
                };
            };
        };
        return redirect('/app');
    }

    /**
     * Microsoft oAuth
     *
     * @param mixed $request
     * @return void
     */
    public function MicrosoftOauth(Request $request)
    {

        if (!empty($request->query('state')) && $request->query('state') != '' && !empty($request->query('code')) && $request->query('code') != '') {
            $microsoftUser = Socialite::driver('microsoft')->stateless()->user();

            $check = User::where('microsoft_id', $microsoftUser->id)->count();

            if ($check) {
                $user = User::where('microsoft_id', $microsoftUser->id)->first();
                Auth::login($user);
            } else {
                if (Auth::check() == true) {
                    User::where('id', Auth::id())
                        ->update([
                            'microsoft_id' => $microsoftUser->id,
                            'updated_at' => Carbon::now()
                        ]);
                    return redirect(route('settings.index'));
                } else {
                    $user = new User;

                    $user->email = $microsoftUser->email;
                    $user->password = Hash::make(Str::random(30));
                    $user->microsoft_id = $microsoftUser->id;
                    $user->email_verified_at = Carbon::now();
                    $user->created_at = Carbon::now();

                    $user->save();

                    DB::table('users_settings')->insert([
                        'user_id' => $user->id,
                        'created_at' => Carbon::now()
                    ]);

                    DB::table('modules')->insert([
                        'user_id' => $user->id,
                        'created_at' => Carbon::now()
                    ]);

                    Auth::login($user);
                };
            };
        };
        return redirect('/app');
    }
}
