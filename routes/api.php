<?php

use Illuminate\Http\Request;
use App\Http\Controllers\API\RegisterController;
use Illuminate\Support\Facades\Route;


Route::middleware('api')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    })->middleware('auth:sanctum');

    Route::controller(RegisterController::class)->group(function () {
        Route::post('register', 'register');
        Route::post('login', 'login');
    });
});
