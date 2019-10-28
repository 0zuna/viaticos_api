<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
Route::resource('/viaje', 'admin\AdminController');
Route::get('/users', 'admin\AdminController@users');
Route::post('/gasto', 'admin\AdminController@gasto');
Route::post('/anticipo', 'admin\AdminController@anticipo');
Route::delete('/deletegasto', 'admin\AdminController@deletegasto');
Route::delete('/deleteviaje', 'admin\AdminController@deleteviaje');
Route::delete('/deleteanticipo', 'admin\AdminController@deleteanticipo');
Route::get('/excel', 'admin\AdminController@excel');
Route::get('/excel_viaje', 'admin\AdminController@excel_viaje');
Route::post('/adeudos', 'admin\AdminController@adeudos');
