<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function respondWithToken($token, $responseMessage, $data,$role="manager"){
        return \response()->json([
        "success" => true,
        "message" => $responseMessage,
        "data" => $data,
        "token" => $token,
        'role' => $role,
        "token_type" => "bearer",
        ],200);
        }
}
