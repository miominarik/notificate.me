<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;


    public function __construct()
    {
        $this->key = '1b70b24466f996ec7778537030abab146b577267833dca7095e6a3c833469e588604f21b75dd2e9e2578c4eb8e0ad3c78e4c2c76fe52b75ce5a228cf1f1119ec';
    }

    public function JWT_encode($data)
    {
        $data = [$data];

        return JWT::encode($data, $this->key, 'HS512');
    }

    public function JWT_decode($data)
    {
        return (array)JWT::decode($data, new Key($this->key, 'HS512'));
    }

    /**
     * @param string $log_type - Názov daného logu
     * @param string $ip_address - IP adresa
     * @param int $task_id - Ak je to task, tak jeho id. Inak sa uloži NULL
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

}
