<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PanicController;
use App\Http\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

/* The specified routes below have "/api" added before since the api route file is used */
Route::post("/login", [UserController::class, "login"]);

/* Secured routes */
Route::group(["middleware" => "auth:sanctum"], function() {
    Route::get("/panic_history", [PanicController::class, "panic_history"]);
    Route::post("/send_panic", [PanicController::class, "send_panic"]);
    Route::post("/cancel_panic", [PanicController::class, "cancel_panic"]);
    Route::post("/logout", [UserController::class, "logout"]);
});