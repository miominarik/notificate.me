<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class AuthSuperAdminUser
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {

        $superadmin_db = DB::table('users')
            ->select('superadmin')
            ->where('id', Auth::id())
            ->get();

        if (isset($superadmin_db[0]->superadmin)) {
            if ($superadmin_db[0]->superadmin === 1) {
                if (Session::get('user_superadmin') !== null) {
                    if (Session::get('user_superadmin') === 1) {
                        return $next($request);
                    } else {
                        return redirect(route('tasks.index'));
                    };
                } else {
                    return redirect(route('tasks.index'));
                };
            } else {
                return redirect(route('tasks.index'));
            };
        } else {
            return redirect(route('tasks.index'));
        };
        return redirect(route('tasks.index'));
    }
}
