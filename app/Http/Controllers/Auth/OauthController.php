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
        /* $this->middleware('guest')->except('logout'); */
        //$this->middleware(VerifyGoogleRecaptcha::class);
    }

    public function GithubOauth(Request $request)
    {

        if (empty($request->query('error')) && $request->query('error') != 'access_denied') {
            $githubUser = Socialite::driver('github')->user();

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
}
