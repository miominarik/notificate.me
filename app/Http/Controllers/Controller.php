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

}
