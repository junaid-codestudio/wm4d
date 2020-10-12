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

	/* Cloudways API Management */
	Route::get('get_api_token', 'App\Http\Controllers\CloudwaysController@index');
	Route::get('check_operation_status/{operation_id?}', 'App\Http\Controllers\CloudwaysController@checkOperationStatus');

	/* Server Management Operations */
	Route::get('get_server_list', 'App\Http\Controllers\ServerManagementController@getServerList')->name('server.list');
	Route::post('get_server_resource_consumption', 'App\Http\Controllers\ServerManagementController@getMonitoringGraph')->name('server.monitoringgraph');

	/* Site Management Operations */
	Route::get('get_site_list/{server_id?}', 'App\Http\Controllers\SiteManagementController@getSitesList')->name('sites.list');
	Route::post('add_application', 'App\Http\Controllers\SiteManagementController@addApp')->name('add.app');
	Route::post('clone_application', 'App\Http\Controllers\SiteManagementController@cloneApp')->name('clone.app');
	Route::post('clone_application_to_other_server', 'App\Http\Controllers\SiteManagementController@cloneToOtherServer')->name('clone.app.to.other.server');
	Route::post('clone_staging_app', 'App\Http\Controllers\SiteManagementController@cloneStagingApp')->name('clone.staging.app');
	Route::post('clone_staging_application_to_other_server', 'App\Http\Controllers\SiteManagementController@cloneStagingAppToOtherServer')->name('clone.staging.app.to.other.server');
	Route::post('delete_application', 'App\Http\Controllers\SiteManagementController@deleteApp')->name('delete.app');
	Route::post('update_application_label', 'App\Http\Controllers\SiteManagementController@updateAppLabel')->name('update.app.label');
	// Route::post('get_server_resource_consumption', 'App\Http\Controllers\ServerManagementController@getMonitoringGraph')->name('server.monitoringgraph');

	/* clients operations */
	Route::get('save_accounts_list', 'App\Http\Controllers\ClientsController@saveMarchexAccountsList')->name('clients.acct.list.save');
	Route::get('get_clients_list/{account_id?}', 'App\Http\Controllers\ClientsController@getMarchexClientsList')->name('clients.user.list');
	Route::get('get_all_clients', 'App\Http\Controllers\ClientsController@getAllClients')->name('clients.list.all');
	Route::get('get_active_clients', 'App\Http\Controllers\ClientsController@getActiveClients')->name('clients.list.active');
	Route::get('get_all_marchex_clients', 'App\Http\Controllers\ClientsController@getAllMarchexClients')->name('clients.list.marchex.all');
	Route::get('get_active_marchex_clients', 'App\Http\Controllers\ClientsController@getActiveMarchexClients')->name('clients.list.marchex.active');
	Route::post('add_new_client', 'App\Http\Controllers\ClientsController@store')->name('clients.add');
	Route::post('update_client', 'App\Http\Controllers\ClientsController@update')->name('clients.update');
	Route::get('delete_client/{client_id?}', 'App\Http\Controllers\ClientsController@delete')->name('clients.delete');
	Route::get('restore_client/{client_id?}', 'App\Http\Controllers\ClientsController@restore')->name('clients.restore');
});
/* Route::middleware('jwt.auth')->group(function() {
	Route::get("auth", function(){
		echo 'here';
	});
}); */

Route::middleware('auth:sanctum')->group(function() {
	Route::get("auth", function(){
		echo 'sanctum';
	});
});
