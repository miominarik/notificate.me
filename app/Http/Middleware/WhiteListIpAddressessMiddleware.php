<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WhiteListIpAddressessMiddleware
{
    /**
     * Check if actual IP adress is in DB as allowed
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $startIp = ip2long('10.31.0.0');
        $endIp = ip2long('10.31.63.255');
        $requestIp = ip2long($request->ip());

        if (($requestIp >= $startIp && $requestIp <= $endIp) || ($request->ip() == '213.160.175.138' || $request->ip() == '127.0.0.1')) {
            return $next($request);
        }else{
            return response()->json([
                'status' => 'error',
                'note' => 'You are restricted to access the site'
            ], 403);
        };
    }
}
