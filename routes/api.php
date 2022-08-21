<?php

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

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\UserController;
use App\Http\Controllers\PostController;

Route::group(['middleware' => 'auth:api'], function () {
    Route::post('logout', [LoginController::class, 'logout']);

    // posts module routes
    Route::controller(PostController::class)->group(function (){
        Route::get('posts', 'index');
        Route::post('posts', 'store');
        Route::post('posts/{post}', 'update');
        Route::delete('posts/{post}', 'destroy');
    });

    // User routes
    Route::controller(UserController::class)->group(function (){
        Route::get('me', 'current');
        Route::post('me', 'update');

        Route::get('users', 'index');
        Route::post('users', 'store');
        Route::post('users/{post}', 'update');
        Route::delete('users/{post}', 'destroy');
    });

});

Route::group(['middleware' => 'guest:api'], function () {
    Route::post('login', [LoginController::class, 'login']);
    Route::post('register', [LoginController::class, 'register']);
});
