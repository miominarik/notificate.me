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
        return $next($request);
        
        $startIp = ip2long('10.31.0.0');
        $endIp = ip2long('10.31.63.255');
        $requestIp = ip2long($_SERVER['HTTP_CF_CONNECTING_IP']);

        if (($_SERVER['HTTP_CF_CONNECTING_IP'] == '38.242.235.30' || $_SERVER['HTTP_CF_CONNECTING_IP'] == '2a02:c206:2137:1751:0000:0000:0000:0001' || $_SERVER['HTTP_CF_CONNECTING_IP'] == '127.0.0.1')) {
            return $next($request);
        }else{
            return response()->json([
                'status' => 'error',
                'note' => 'You are restricted to access the site',
                'ip' => $_SERVER['HTTP_CF_CONNECTING_IP']
            ], 403);
        };
    }
}
