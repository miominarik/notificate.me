<?php

namespace App\Http\Controllers;

use App\Http\Requests\EditSettingsRequest;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
                'updated_at' => Carbon::now()
            ]);
        return redirect(route('settings.index'))->with('status_success', 'Nastavenia boli uložené');
    }
}
