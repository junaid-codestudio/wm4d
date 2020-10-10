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

Route::middleware('auth.publicapikey')->group(function() {
	Route::post("login", "\App\Http\Controllers\UserController@login")->name('login');
	Route::get('ssh_conn', 'App\Http\Controllers\SShController@connect');
	Route::get('get_api_token', 'App\Http\Controllers\CloudwaysController@index');
	Route::get('get_server_list', 'App\Http\Controllers\CloudwaysController@getServerList');
	Route::get('get_server_disk_usage/{server_id?}', 'App\Http\Controllers\CloudwaysController@getServerDiscUsage');
	Route::get('check_operation_status/{operation_id?}', 'App\Http\Controllers\CloudwaysController@checkOperationStatus');
});
Route::middleware('jwt.auth')->group(function() {
	Route::get("auth", function(){
		echo 'here';
	});
});

Route::middleware('auth:sanctum')->group(function() {
	Route::get("auth", function(){
		echo 'sanctum';
	});
});
