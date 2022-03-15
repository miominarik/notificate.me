<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WhiteListIpAddressessMiddleware
{
    public function get_ip()
    {
        $data = DB::table('api')
            ->select('ip')
            ->where('enabled', true)
            ->get();

        $ip_list = array();

        if ($data) {
            foreach ($data as $item) {
                array_push($ip_list, $item->ip);
            }

            return $ip_list;
        }
    }
    /**
     * Check if actual IP adress is in DB as allowed
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (!in_array($request->getClientIp(), $this->get_ip())) {
            return response()->json([
                'status' => 'error',
                'note' => 'You are restricted to access the site'
            ], 403);
        }

        return $next($request);
    }
}
