<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CheckActivatedModules
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, $module_name)
    {

        if (isset($module_name)) {
            $data = DB::table('modules')
                ->select($module_name)
                ->where('user_id', Auth::id())
                ->get();

            if (isset($data[0])) {
                if ($data[0]->$module_name == true) {
                    return $next($request);
                } else {
                    return redirect()->back();
                };
            } else {
                return redirect()->back();
            };
        } else {
            return redirect()->back();
        };

        return redirect()->back();

    }
}
