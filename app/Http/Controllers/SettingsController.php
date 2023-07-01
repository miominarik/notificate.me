<?php

namespace App\Http\Controllers;

use App\Http\Requests\ChangePasswordRequest;
use App\Http\Requests\EditSettingsRequest;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class SettingsController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('settings.Index', [
            'settings_data' => DB::table('users_settings')
                ->where('user_id', Auth::id())
                ->get(),
            'modules_status' => DB::table('modules')
                ->select('module_sms')
                ->where('user_id', Auth::id())
                ->get(),
            'calendar_hash' => Auth::user()->email,
            'my_devices' => DB::table('fcm_tokens')
                ->select('device_model', 'updated_at', 'id')
                ->where('user_id', '=', Auth::id())
                ->where('enabled', '=', 1)
                ->orderByDesc('updated_at')
                ->get(),
            'mfa_info' => DB::table('mfa_authorization')
                ->where('user_id', '=', Auth::id())
                ->where('enabled', '=', 1)
                ->get(),
            'ics_sources' => DB::table('ics_sources')
                ->where("user_id", "=", Auth::id())
                ->get()
        ]);
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
                if (Hash::check($oldpass, $actual_pass_db[0]->password) == TRUE) {
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

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     *
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

    public function Email_unsubscribe($user_email)
    {
        $status = 0;
        if ($user_email) {
            $user_id = DB::table('users')
                ->select('id')
                ->where('email', '=', $user_email)
                ->get();

            if (isset($user_id[0])) {
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

    /**
     * Odpojenie všetkých devicov. Zrušenie sesions
     * @return void
     */
    public function disconnect_all_devices()
    {
        DB::table('sessions')
            ->where('user_id', '=', Auth::id())
            ->delete();
        DB::table('fcm_tokens')
            ->where('user_id', '=', Auth::id())
            ->delete();

        return redirect(route('settings.index'));
    }

    public function VerifyMfaCode(int $device_id, int $user_id)
    {
        if (isset($device_id) && !empty($device_id) && is_numeric($device_id) && $device_id > 0 && isset($user_id) && !empty($user_id) && is_numeric($user_id) && $user_id > 0) {
            $verify_device_id = DB::table('fcm_tokens')
                ->where('id', '=', $device_id)
                ->where('user_id', '=', $user_id)
                ->where('enabled', '=', 1)
                ->count();

            if ($verify_device_id > 0) {
                $get_fcm_token = DB::table('fcm_tokens')
                    ->select('fcm_token')
                    ->where('id', '=', $device_id)
                    ->where('user_id', '=', $user_id)
                    ->where('enabled', '=', 1)
                    ->get();

                if ($get_fcm_token->count() > 0) {

                    $rand_code = rand(1000, 32766);

                    DB::table('mfa_codes')
                        ->insert([
                            'user_id' => $user_id,
                            'device_id' => $device_id,
                            'code' => $rand_code,
                            'used' => 0,
                            'created_at' => Carbon::now()
                        ]);

                    $body = "Kód: " . $rand_code . ". ";
                    $body .= "Overovací kód pre dvojfaktorové prihlásenie. ";

                    $apiController = new ApiController();
                    $status = $apiController->sendNotification("Notificate.me", $body, $user_id, $get_fcm_token[0]->fcm_token);

                    if (empty($status)) {
                        return 1;
                    } else {
                        return 2;
                    };
                } else {
                    return 2;
                };
            } else {
                return 2;
            };
        }
    }

    public function CheckMfaCode(Request $request)
    {
        $validated = $request->validate([
            'device_id' => 'numeric|required|min:1',
            'user_id' => 'numeric|required|min:1',
            'mfacode' => 'numeric|required|min:1000'
        ]);

        $verify = DB::table('mfa_codes')
            ->select('code')
            ->where('device_id', '=', $validated['device_id'])
            ->where('user_id', '=', $validated['user_id'])
            ->where('used', '=', 0)
            ->orderByDesc('id')
            ->whereDate('created_at', '>', Carbon::now()->subDays(1))
            ->limit(1)
            ->get();

        if ($verify->count() > 0) {
            $recovery_words = "";
            for ($i = 1; $i <= 10; $i++) {
                if ($recovery_words == "") {
                    $recovery_words .= strtoupper($this->getrandomstring(10));
                } else {
                    $recovery_words .= "," . strtoupper($this->getrandomstring(10));
                };
            }

            if ($verify[0]->code == $validated['mfacode']) {
                DB::table('mfa_authorization')
                    ->insert([
                        'user_id' => $validated['user_id'],
                        'device_id' => $validated['device_id'],
                        'enabled' => 1,
                        'recovery_codes' => Crypt::encrypt($recovery_words),
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ]);

                DB::table('mfa_codes')
                    ->where('device_id', '=', $validated['device_id'])
                    ->where('user_id', '=', $validated['user_id'])
                    ->where('code', '=', $validated['mfacode'])
                    ->update([
                        'used' => 1,
                        'updated_at' => Carbon::now()
                    ]);

                return redirect()->back()->with('status_success', __('settings.mfa_success'));
            } else {
                return redirect()->back()->with('status_warning', __('settings.mfa_warning'));
            };
        } else {
            return redirect()->back()->with('status_warning', __('settings.mfa_warning'));
        };
    }

    public function DisableMFA()
    {
        DB::table('mfa_authorization')
            ->where('user_id', '=', Auth::id())
            ->delete();
        return redirect()->back()->with('status_success', 'Dvojfaktorové prihlásenie bolo úspešne deaktivované');
    }

    public function add_ics_source(Request $request)
    {
        $validated = $request->validate([
            'ics_name' => 'string|required|min:1|max:255',
            'ics_url' => 'URL|required',
            'ics_notif' => 'boolean'
        ]);

        $inserted_id = DB::table('ics_sources')
            ->insertGetId([
                'user_id' => Auth::id(),
                'name' => $validated['ics_name'],
                'ics_url' => $validated['ics_url'],
                'allow_notif' => $validated['ics_notif'],
                'created_at' => Carbon::now()
            ]);
        if ($inserted_id) {
            return redirect()->back()->with('status_success', __('settings.ics_add_success'));
        }
    }

    public function remove_ics_source(int $ics_id)
    {
        if ($ics_id > 0) {
            $check_if_exist = DB::table("ics_sources")
                ->where('user_id', '=', Auth::id())
                ->where('id', '=', $ics_id)
                ->count();
            if ($check_if_exist > 0) {
                //Remove all tasks
                DB::table('tasks')
                    ->where('user_id', '=', Auth::id())
                    ->where('ics_source_id', '=', $ics_id)
                    ->delete();
                DB::table('ics_sources')
                    ->where('user_id', '=', Auth::id())
                    ->where('id', '=', $ics_id)
                    ->delete();
                return redirect()->back()->with('status_success', __('settings.ics_remove_success'));
                
            }
        }
        return redirect()->back()->with('status_danger', __('settings.ics_remove_fail'));
    }
}
