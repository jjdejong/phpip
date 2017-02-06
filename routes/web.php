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
	Route::get('matter/export', 'MatterController@export');
	Route::get('matter/{matter}', 'MatterController@view'); // ->middleware('can:view,matter');

	Route::get('matter/{id}/events', function ($id) {
		$matter = App\Matter::find($id);
	    return $matter->events;
	});

	Route::get('matter/{id}/tasks', function ($id) {
		$matter = App\Matter::find($id);
	    return $matter->tasks->where('code', '!=', 'REN'); // Renewals excluded
	});
	
	Route::get('matter/{id}/renewals', function ($id) {
		$matter = App\Matter::find($id);
		return $matter->tasks->where('code', 'REN'); // Renewals
	});

	Route::get('matter/{id}/actors', function ($id) {
		$matter = App\Matter::find($id);
	    if ( $matter->container_id ) {
			return $matter->actors->toBase()
			->merge( $matter->container->actors->where('pivot.shared', 1) )
			->groupBy('pivot.role')
			->sortBy('pivot.display_order');
	    } else {
	    	return $matter->actors
	    	->groupBy('pivot.role')
	    	->sortBy('pivot.display_order');
	    }
	});
	
	Route::get('matter/{id}/classifiers', function ($id) {
		$matter = App\Matter::find($id);
		return $matter->classifiers;
	});
	
	Route::get('matter/{id}/category', function ($id) {
		$matter = App\Matter::find($id);
		return $matter->category;
	});
	
	Route::get('matter/{id}/type', function ($id) {
		$matter = App\Matter::find($id);
		return $matter->type;
	});

	Route::get('matter/{id}/country', function ($id) {
		$matter = App\Matter::find($id);
		return $matter->countryInfo;
	});
	
	Route::get('matter/{id}/origin', function ($id) {
		$matter = App\Matter::find($id);
		return $matter->originInfo;
	});

	Route::get('actor/{id}', function ($id) {
		return App\Actor::find($id);
	});

	Route::get('actor/search/{term}', function ($term) {
		return App\Actor::where('name', 'like', "%$term%")->simplePaginate(25);
	});
	
	Route::get('role', function () {
		return App\Role::all();
	});
});
