<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Firebase\JWT\Key;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Firebase\JWT\JWT;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function JWT_encode($data)
    {
        if (Cookie::has('token')) {
            $decorder_key = Cookie::get('token');
        } else {
            $decorder_key = "NotificateME578565475GFFGGG35677866856GJGHFJHG2G3FFG2HGHG3HTR627868762786GHJG3HG2J2UI63762763HGH2HJ3G2";
        };

        $data = [$data];

        return JWT::encode($data, $decorder_key, 'HS512');
    }

    public function JWT_decode($data)
    {
        if (Cookie::has('token')) {
            $decorder_key = Cookie::get('token');
        } else {
            $decorder_key = "NotificateME578565475GFFGGG35677866856GJGHFJHG2G3FFG2HGHG3HTR627868762786GHJG3HG2J2UI63762763HGH2HJ3G2";
        };
        return (array)JWT::decode($data, new Key($decorder_key, 'HS512'));
    }

    /**
     * @param string $log_type - Názov daného logu
     * @param string $ip_address - IP adresa
     * @param int $task_id - Ak je to task, tak jeho id. Inak sa uloži NULL
     *
     * @return void
     */
    protected function add_log($log_type, $ip_address, $task_id, $date = NULL)
    {

        if (Auth::check()) {
            if (!isset($task_id) || empty($task_id) || $task_id == 0) {
                $task_id = NULL;
            };

            if (!isset($date) || empty($date) || $date == NULL) {
                $date = Carbon::now();
            };

            if (isset($log_type)) {
                DB::table('logs')
                    ->insert([
                        'user_id' => Auth::id(),
                        'log_type' => $log_type,
                        'task_id' => $task_id,
                        'ip_address' => $ip_address,
                        'created_at' => $date
                    ]);
            }
        }
    }

    protected function Generate_cookie($old_token = NULL)
    {

        if (isset($old_token) && !empty($old_token) && $old_token != NULL) {
            DB::table('sessions')
                ->where('token', '=', $old_token)
                ->delete();
        };

        //generate token
        $token = Hash::make(Auth::id());

        $expires = time() + 60 * 60 * 24 * 365;
        Cookie::queue('token', $token, $expires);

        DB::table('sessions')
            ->insert([
                'user_id' => Auth::id(),
                'token' => $token,
                'last_used' => Carbon::now()
            ]);
    }

    public function VerifyRecaptcha(Request $request)
    {
        if ($request->input('action') !== NULL && $request->input('g-recaptcha-response') !== NULL && $request->input('action') === 'validate_captcha') {
            $captcha = $request->input('g-recaptcha-response');
        } else {
            $captcha = FALSE;
        };

        if ($captcha == FALSE) {
            return die("Captcha error");
        } else {
            $secret = env('RECAPTCHA_V3_SECRET_KEY');
            $response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=" . $secret . "&response=" . $captcha . "&remoteip=" . $request->ip());
            $response = json_decode($response);

            if ($response->success !== TRUE || $response->score < 0.7 || $response->action !== 'validate_captcha') {
                return die("Captcha error");
            };
        }

    }

    public function formatBytes($size, $precision = 2)
    {
        $base = log((float)$size, 1024);
        $suffixes = array('', 'Kb', 'Mb', 'Gb', 'Tb');

        return round(pow(1024, $base - floor($base)), $precision) . ' ' . $suffixes[floor($base)];
    }

}
