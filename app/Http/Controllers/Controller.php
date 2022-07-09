<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function JWT_encode($data)
    {
        $data = [$data];

        return JWT::encode($data, env('JWT_SECRET'), 'HS512');
    }

    public function JWT_decode($data)
    {
        return (array)JWT::decode($data, new Key(env('JWT_SECRET'), 'HS512'));
    }

}
