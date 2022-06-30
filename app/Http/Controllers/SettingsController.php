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

        DB::table('users_settings')
            ->where('user_id', Auth::id())
            ->update([
                'enable_email_notification' => $validated['enable_email_notif'],
                'notification_time' => $validated['notification_time'],
                'mobile_number' => $validated['mobile_number'],
                'color_palette' => isset($validated['color_palette']) ? $validated['color_palette'] : 1,
                'updated_at' => Carbon::now()
            ]);

        if (isset($validated['color_palette']) && !empty($validated['color_palette'])) {
            $color_palette = match ($validated['color_palette']) {
                '1' => 'primary',
                '2' => 'success',
                '3' => 'warning',
                '4' => 'danger',
                '5' => 'dark',
                '6' => 'light',
                '7' => 'secondary',
                default => 'primary'
            };

            $color_scheme = match ($validated['color_palette']) {
                '1' => 'navbar-dark',
                '2' => 'navbar-dark',
                '3' => 'navbar-dark',
                '4' => 'navbar-dark',
                '5' => 'navbar-dark',
                '6' => 'navbar-light',
                '7' => 'navbar-dark',
                default => 'navbar-dark'
            };
        } else {
            $color_palette = 'primary';
            $color_scheme = 'navbar-dark';
        };

        Session::put('color_palette', $color_palette);
        Session::put('color_scheme', $color_scheme);


        return redirect(route('settings.index'))->with('status_success', 'Nastavenia boli uložené');
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

                            return redirect()->back()->with('status_success', 'Heslo bolo úspešne zmenené');

                        } else {
                            return redirect()->back()->with('status_warning', 'Heslo musí obsahovať viac ako 8 znakov');
                        };
                    } else {
                        return redirect()->back()->with('status_warning', 'Nové heslá sa nezhodujú');
                    };
                } else {
                    return redirect()->back()->with('status_warning', 'Pôvodné heslá sa nezhodujú');
                };
            } else {
                return redirect()->back()->with('status_danger', 'Nastala chyba pri zmene hesla');
            };
        } else {
            return redirect()->back()->with('status_danger', 'Nastala chyba pri zmene hesla');
        };
    }
}
