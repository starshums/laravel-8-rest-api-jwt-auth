<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::group([ "middleware" => "api", "prefix" => "auth", "namespace" => "App\Http\Controllers\API"], function ($router) {
    Route::post("login", "AuthController@login");
    Route::post("register", "AuthController@register");
    Route::post("logout", "AuthController@logout");
    Route::post("refresh", "AuthController@refresh");
    Route::get("me", "AuthController@me");
});

Route::group([ "middleware" => "api", "namespace" => "App\Http\Controllers\API"], function ($router) {
    Route::resource("posts", "PostController");
});
