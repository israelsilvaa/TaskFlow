<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;


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

            return response()->json([
                "success" => [
                    "status" => "201", "title" => "Created", "detail" => $users
                ]
            ], 201);
        } catch (Exception $e) {

            return response()->json([
                "error" => [
                    "status" => "500",
                    "title" => "Internal Server Error",
                    "detail" => $e->getMessage(),
                ]
            ], 500);
        }
    }
}
