<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ModulesController extends Controller
{
    public function Users_modules()
    {
        $data = DB::table('modules')
            ->select('module_sms', 'module_calendar')
            ->where('user_id', Auth::id())
            ->get();

        if (isset($data[0])) {
            return $data[0];
        } else {
            return NULL;
        };

    }

    public function index()
    {
        return view('modules.index', [
            'modules_status' => DB::table('modules')
                ->select('module_sms', 'module_calendar')
                ->where('user_id', Auth::id())
                ->get()
        ]);
    }

    public function activate_modul(Request $request)
    {
        $allowed_modules = [
            'module_sms', 'module_calendar'
        ];

        if (isset($request->module_name) && !empty($request->module_name)) {
            if (in_array($request->module_name, $allowed_modules)) {
                $check_if_is_not_enabled_yet = DB::table('modules')
                    ->select($request->module_name)
                    ->where('user_id', Auth::id())
                    ->get();
                if ($check_if_is_not_enabled_yet[0]->{$request->module_name} == 0) {
                    $activate_status = DB::table('modules')
                        ->where('user_id', Auth::id())
                        ->update([
                            $request->module_name => 1
                        ]);
                    if ($activate_status) {
                        $this->add_log('Activate ' . $request->module_name . '', $request->ip(), 0);
                        return redirect(route('modules.index'))->with('status_success', __('alerts.modules_activated'));
                    } else {
                        return redirect(route('modules.index'))->with('status_danger', __('alerts.modules_activated_error'));
                    };
                } else {
                    return redirect(route('modules.index'))->with('status_warning', __('alerts.modules_activated_already'));
                };
            } else {
                return redirect(route('modules.index'))->with('status_danger', __('alerts.modules_activated_missing'));
            };
        } else {
            return redirect(route('modules.index'))->with('status_danger', __('alerts.modules_activated_error'));
        };
    }

    public function deactivate_modul(Request $request)
    {
        $allowed_modules = [
            'module_sms', 'module_calendar'
        ];

        if (isset($request->module_name) && !empty($request->module_name)) {
            if (in_array($request->module_name, $allowed_modules)) {
                $check_if_is_is_enabled_yet = DB::table('modules')
                    ->select($request->module_name)
                    ->where('user_id', Auth::id())
                    ->get();
                if ($check_if_is_is_enabled_yet[0]->{$request->module_name} == 1) {
                    $activate_status = DB::table('modules')
                        ->where('user_id', Auth::id())
                        ->update([
                            $request->module_name => 0
                        ]);
                    if ($activate_status) {
                        $this->add_log('Deactivate ' . $request->module_name . '', $request->ip(), 0);
                        return redirect(route('modules.index'))->with('status_success', __('alerts.modules_deactivated'));
                    } else {
                        return redirect(route('modules.index'))->with('status_danger', __('alerts.modules_deactivated_error'));
                    };
                } else {
                    return redirect(route('modules.index'))->with('status_warning', __('alerts.modules_deactivated_already'));
                };
            } else {
                return redirect(route('modules.index'))->with('status_danger', __('alerts.modules_deactivated_missing'));
            };
        } else {
            return redirect(route('modules.index'))->with('status_danger', __('alerts.modules_deactivated_error'));
        };
    }
}
