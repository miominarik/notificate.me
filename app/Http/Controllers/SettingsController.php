<?php

namespace App\Http\Controllers;

use App\Http\Requests\ChangePasswordRequest;
use App\Http\Requests\EditSettingsRequest;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class SettingsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('settings.Index', [
            'settings_data' => DB::table('users_settings')
                ->where('user_id', Auth::id())
                ->get(),
            'apple_oauth_status' => DB::table('users')
                ->select('apple_id')
                ->where('id', Auth::id())
                ->get(),
            'google_oauth_status' => DB::table('users')
                ->select('google_id')
                ->where('id', Auth::id())
                ->get(),
            'microsoft_oauth_status' => DB::table('users')
                ->select('microsoft_id')
                ->where('id', Auth::id())
                ->get(),
            'modules_status' => DB::table('modules')
                ->select('module_sms')
                ->where('user_id', Auth::id())
                ->get()
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(EditSettingsRequest $request)
    {

        $validated = $request->validated();

        $validated['notification_time'] = $validated['notification_time'] . ":00";
        $validated['mobile_number'] = trim(str_replace(array("'", "\"", ";", "\\", "?", "&", "@", ":", "/", "#", "$", "=", ">", "<", "+", " "), "", $validated['mobile_number']));

        if (in_array($validated['language'], ['en', 'sk'])) {
            app()->setLocale($validated['language']);
            session()->put('locale', $validated['language']);

            DB::table('users_settings')
                ->where('user_id', Auth::id())
                ->update([
                    'enable_email_notification' => $validated['enable_email_notif'],
                    'notification_time' => $validated['notification_time'],
                    'mobile_number' => $validated['mobile_number'],
                    'language' => $validated['language'],
                    'updated_at' => Carbon::now()
                ]);

        };

        $this->add_log('Update Profile', $request->ip(), 0);

        return redirect(route('settings.index'))->with('status_success', __('alerts.settings_updated'));
    }

    public function change_password(ChangePasswordRequest $request)
    {
        $validated = $request->validated();

        $oldpass = $validated['oldpass'];
        $newpass1 = $validated['newpass1'];
        $newpass2 = $validated['newpass2'];
        $logout_everywhere = isset($validated['logout_everywhere']) || !empty($validated['logout_everywhere']) ? $validated['logout_everywhere'] : NULL;


        if (isset($oldpass) && isset($newpass1) && isset($newpass2)) {
            //Získanie aktuálneho hesla z DB
            $actual_pass_db = DB::table('users')
                ->select('password')
                ->where('id', Auth::id())
                ->get();

            if (isset($actual_pass_db[0]->password)) {
                //Porovnaj stare heslo z formu a db
                if (Hash::check($oldpass, $actual_pass_db[0]->password) == true) {
                    //Skontroluj či nové hesla sú rovnaké
                    if ($newpass2 === $newpass1) {
                        if (strlen($newpass1) >= 8) {
                            DB::table('users')
                                ->where('id', Auth::id())
                                ->update([
                                    'password' => Hash::make($newpass1),
                                    'updated_at' => Carbon::now()
                                ]);

                            //Kontrola či sa chceme aj odhlasiť na ostatných zariadeniach
                            if (isset($logout_everywhere) && $logout_everywhere != NULL && $logout_everywhere == 'on') {
                                Auth::logoutOtherDevices($newpass1);
                            };

                            $this->add_log('Change Password', $request->ip(), 0);

                            return redirect()->back()->with('status_success', __('alerts.settings_change_pass_succ'));

                        } else {
                            return redirect()->back()->with('status_warning', __('alerts.settings_change_pass_8'));
                        };
                    } else {
                        return redirect()->back()->with('status_warning', __('alerts.settings_change_pass_nerovna'));
                    };
                } else {
                    return redirect()->back()->with('status_warning', __('alerts.settings_change_pass_nerovna_old'));
                };
            } else {
                return redirect()->back()->with('status_danger', __('alerts.settings_change_pass_error'));
            };
        } else {
            return redirect()->back()->with('status_danger', __('alerts.settings_change_pass_error'));
        };
    }

    public function Email_unsubscribe($user_email)
    {
        $status = 0;
        if ($user_email) {
            $user_id = DB::table('users')
                ->select('id')
                ->where('email', '=', $user_email)
                ->get();

            if ($user_id[0]) {
                $check_status = DB::table('users_settings')
                    ->select('enable_email_notification')
                    ->where('user_id', '=', $user_id[0]->id)
                    ->get();

                if ($check_status[0]->enable_email_notification == 1) {
                    DB::table('users_settings')
                        ->where('user_id', '=', $user_id[0]->id)
                        ->update([
                            'enable_email_notification' => 0
                        ]);
                    $status = 1;
                }
            }

            return view('home.index', [
                'page' => 'unsubscribe',
                'status' => $status
            ]);

        }
    }
}
