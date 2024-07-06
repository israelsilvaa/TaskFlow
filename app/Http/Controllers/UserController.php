<?php

namespace App\Http\Controllers;

use Tymon\JWTAuth\Facades\JWTAuth;
use App\Http\Services\ApiServices;
use App\Models\User;
use Exception;


class UserController extends Controller
{

    /**
     * Display a listing of users.
     */
    public function usersAll()
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            $users = User::where('id', '!=', $user->id)->get();

            return ApiServices::statusCode200($users);

        } catch (Exception $e) {

            ApiServices::statusCode500($e->getMessage());

        }
    }
}
