<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\StatusController;
use App\Http\Controllers\TaskController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['prefix' => 'v1/'], function () {

    Route::apiResource('task', TaskController::class)->middleware('jwt.auth');
    Route::get('users', [TaskController::class, 'usersAll'])->middleware('jwt.auth');
    Route::get('status', [StatusController::class, 'index']);
    
});

Route::post('login', [AuthController::class, 'login'])->name('login');
Route::post('register', [AuthController::class, 'register'])->name('register');
Route::post('refresh', [AuthController::class, 'refresh']);
Route::post('me', [AuthController::class, 'me'])->middleware('jwt.auth');
Route::post('logout', [AuthController::class, 'logout'])->middleware('jwt.auth');
