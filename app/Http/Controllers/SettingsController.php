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
        return view('settings.index', [
            'settings_data' => DB::table('users_settings')
                ->where('user_id', Auth::id())
                ->get()
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(EditSettingsRequest $request)
    {

        $validated = $request->validated();

        DB::table('users_settings')
            ->where('user_id', Auth::id())
            ->update([
                'enable_email_notification' => $validated['enable_email_notif'],
                'notification_time' => $validated['notification_time'],
                'updated_at' => Carbon::now()
            ]);
        return redirect(route('settings.index'))->with('status_success', 'Nastavenia boli uložené');
    }
}
