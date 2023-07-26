<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::prefix('/user')->group(function(){
    Route::post('/login', 'App\Http\Controllers\AuthenticationController@login');
    Route::post('/register', 'App\Http\Controllers\AuthenticationController@register');
    Route::middleware('auth:api')->group(function () {
        Route::get('/all', 'App\Http\Controllers\UserController@all');
    });
});