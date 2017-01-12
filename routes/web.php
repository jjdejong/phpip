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
Route::any('/register','HomeController@index');

Route::get('/home', 'HomeController@index');

Route::get('matter', 'MatterController@index')->middleware('auth');
Route::get('matter/filter', 'MatterController@index')->middleware('auth');
Route::get('matter/export', 'MatterController@export')->middleware('auth');
Route::get('matter/{matter}', 'MatterController@view')->middleware('can:view,matter');
