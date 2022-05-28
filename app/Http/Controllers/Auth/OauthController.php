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
     * GithubOauth
     *
     * @param mixed $request
     * @return void
     */
    public function GithubOauth(Request $request)
    {
        if (empty($request->query('error')) && $request->query('error') != 'access_denied') {
            $githubUser = Socialite::driver('github')->stateless()->user();

            $check = User::where('github_id', $githubUser->id)->count();

            if ($check) {
                $user = User::where('github_id', $githubUser->id)->first();
                Auth::login($user);
            } else {
                if (Auth::check() == true) {
                    User::where('id', Auth::id())
                        ->update([
                            'github_id' => $githubUser->id,
                            'github_token' => $githubUser->token,
                            'github_refresh_token' => $githubUser->refreshToken,
                            'updated_at' => Carbon::now()
                        ]);
                    return redirect(route('settings.index'));
                } else {
                    $user = new User;

                    $user->name = $githubUser->name;
                    $user->email = $githubUser->email;
                    $user->password = Hash::make(Str::random(30));
                    $user->github_id = $githubUser->id;
                    $user->github_token = $githubUser->token;
                    $user->github_refresh_token = $githubUser->refreshToken;
                    $user->email_verified_at = Carbon::now();
                    $user->created_at = Carbon::now();

                    $user->save();

                    DB::table('users_settings')->insert([
                        'user_id' => $user->id,
                        'created_at' => Carbon::now()
                    ]);

                    Auth::login($user);
                };
            };
        };
        return redirect('/');
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
                User::where('google_id', $googleUser->id)
                    ->update([
                        'google_token' => $googleUser->token,
                        'updated_at' => Carbon::now()
                    ]);
                Auth::login($user);
            } else {
                if (Auth::check() == true) {
                    User::where('id', Auth::id())
                        ->update([
                            'google_id' => $googleUser->id,
                            'google_token' => $googleUser->token,
                            'updated_at' => Carbon::now()
                        ]);
                    return redirect(route('settings.index'));
                } else {
                    $user = new User;

                    $user->name = $googleUser->name;
                    $user->email = $googleUser->email;
                    $user->password = Hash::make(Str::random(30));
                    $user->google_id = $googleUser->id;
                    $user->google_token = $googleUser->token;
                    $user->email_verified_at = Carbon::now();
                    $user->created_at = Carbon::now();

                    $user->save();

                    DB::table('users_settings')->insert([
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
     * GoogleOauth
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

                    $user->name = $microsoftUser->name;
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

                    Auth::login($user);
                };
            };
        };
        return redirect('/app');
    }
}
