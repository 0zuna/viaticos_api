<?php

use Illuminate\Http\Request;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('login', 'AuthController@login');
Route::post('signup', 'AuthController@signup');

Route::group(['middleware' => 'auth:api'], function() {
	Route::get('logout', 'AuthController@logout');
	Route::get('user', 'AuthController@user');
	Route::resource('viaje', 'ViajeController');
	Route::resource('gasto', 'GastoController');
	Route::resource('anticipo', 'AnticipoController');
	Route::put('extendDate/{viaje}', 'ViajeController@extendDate');
	Route::put('finalizarviaje/{viaje}', 'ViajeController@finalizar');
	Route::resource('viaje', 'admin\AdminController');
	Route::get('users', 'admin\AdminController@users');
	Route::post('gasto', 'admin\AdminController@gasto');
	Route::post('anticipo', 'admin\AdminController@anticipo');
	Route::delete('deletegasto', 'admin\AdminController@deletegasto');
	Route::delete('deleteviaje', 'admin\AdminController@deleteviaje');
	Route::delete('deleteanticipo', 'admin\AdminController@deleteanticipo');
	Route::post('excel', 'admin\AdminController@excel');
	Route::post('excel_viaje', 'admin\AdminController@excel_viaje');
	Route::post('adeudos', 'admin\AdminController@adeudos');
	Route::post('reporte', 'ExcelController@reporte');
});
