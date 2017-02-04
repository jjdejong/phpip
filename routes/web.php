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

Route::group(['middleware' => 'auth'], function () {
	Route::get('matter', 'MatterController@index');
	Route::get('matter/filter', 'MatterController@index');
	Route::get('matter/export', 'MatterController@export');
	Route::get('matter/{matter}', 'MatterController@view')->middleware('can:view,matter');

	Route::get('matter/{id}/events', function ($id) {
		$matter = App\Matter::find($id);
	    return $matter->events;
	});

	Route::get('matter/{id}/tasks', function ($id) {
		$matter = App\Matter::find($id);
	    return $matter->tasks;
	});

	Route::get('matter/{id}/actors', function ($id) {
		$matter = App\Matter::find($id);
	    return $matter->actors;
	});

	Route::get('actor/{id}', function ($id) {
		return App\Actor::find($id);
	});

	Route::get('actor/search/{term}', function ($term) {
		return App\Actor::where('name', 'like', "%$term%")->simplePaginate(25);
	});
});
