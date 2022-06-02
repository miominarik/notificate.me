<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ModulesController extends Controller
{
    public function index()
    {
        return view('modules.index', [
            'modules_status' => DB::table('modules')
                ->select('module_sms')
                ->where('user_id', Auth::id())
                ->get()
        ]);
    }

    public function activate_modul(Request $request)
    {
        $allowed_modules = [
            'module_sms'
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
                        return redirect(route('modules.index'))->with('status_success', 'Modul bol úspešné aktivovaný');
                    } else {
                        return redirect(route('modules.index'))->with('status_danger', 'Modul sa nepodarilo aktivovať');
                    };
                } else {
                    return redirect(route('modules.index'))->with('status_warning', 'Modul už je aktivovaný');
                };
            } else {
                return redirect(route('modules.index'))->with('status_danger', 'Uvedený modul neexistuje');
            };
        } else {
            return redirect(route('modules.index'))->with('status_danger', 'Modul sa nepodarilo aktivovať');
        };
    }

    public function deactivate_modul(Request $request)
    {
        $allowed_modules = [
            'module_sms'
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
                        return redirect(route('modules.index'))->with('status_success', 'Modul bol úspešné deaktivovaný');
                    } else {
                        return redirect(route('modules.index'))->with('status_danger', 'Modul sa nepodarilo deaktivovať');
                    };
                } else {
                    return redirect(route('modules.index'))->with('status_warning', 'Modul už je deaktivovaný');
                };
            } else {
                return redirect(route('modules.index'))->with('status_danger', 'Uvedený modul neexistuje');
            };
        } else {
            return redirect(route('modules.index'))->with('status_danger', 'Modul sa nepodarilo deaktivovať');
        };
    }
}
