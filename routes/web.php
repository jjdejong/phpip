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

use App\Matter;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();
Route::any('/register','HomeController@index');

Route::get('/home', 'HomeController@index');

Route::group(['middleware' => 'auth'], function () {
	Route::get('matter', 'MatterController@index');
	Route::get('matter/export', 'MatterController@export');
	Route::get('matter/{id}', 'MatterController@show')->middleware('can:view-noclient');

	Route::get('matter/{id}/events', 'EventController@index'); // Not used yet

	Route::get('matter/{id}/tasks', 'TaskController@tasks');
	
	Route::get('matter/{id}/renewals', 'TaskController@renewals');

	Route::get('actor/{id}', function ($id) {
		return App\Actor::find($id);
	});

	Route::get('actor/search/{term}', function ($term) {
		return App\Actor::where('name', 'like', "%$term%")->take(25)->get();
	});
	
	Route::get('role', function () {
		return App\Role::all();
	});
	
	// Testing - not used
		Route::get('matter/{id}/actors', function ($id) {
			$matter = Matter::find($id);
			return $matter->actors()->groupBy('role_name');
		});
		
		Route::get('matter/{id}/classifiers', function ($id) {
			$matter = Matter::find($id);
			return $matter->classifiers;
		});
	
		Route::get('matter/{id}/category', function ($id) {
			$matter = Matter::find($id);
			return $matter->category;
		});

		Route::get('matter/{id}/type', function ($id) {
			$matter = Matter::find($id);
			return $matter->type;
		});

		Route::get('matter/{id}/country', function ($id) {
			$matter = Matter::find($id);
			return $matter->countryInfo;
		});

		Route::get('matter/{id}/origin', function ($id) {
			$matter = Matter::find($id);
			return $matter->originInfo;
		});
							
		Route::get('task/{id}/event', function ($id) {
			$task = App\Task::find($id);
			return $task->event;
		});
		
		Route::get('event/{id}/tasks', function ($id) {
			$event = App\Event::find($id);
			return $event->tasks;
		});
		
		Route::get('event/{id}/link', function ($id) {
			$event = App\Event::find($id);
			return $event->link;
		});
		
		Route::get('events/withlinks', function () {
			$event = App\Event::has('link')->first();
			return $event->link;
		});
		
		Route::get('event/{id}/retrolink', function ($id) {
			$event = App\Event::find($id);
			return $event->retroLink;
		});
		
		Route::get('matter/{id}/container', function ($id) {
			$matter = Matter::find($id);
			return $matter->container;
		});
		
		Route::get('matter/{id}/status', function ($id) {
			$matter = Matter::find($id);
			return $matter->status;
		});
		
		Route::get('matter/status/{term}', function ($term) {
			$matters = Matter::with('status')->whereHas('status', function($q) use ($term) {
				$q->where('name', 'LIKE', "$term%");
			})->take(25)->get();
			return $matters;
		});
		
		Route::get('matter/{id}/priority_to', function ($id) {
			$matter = Matter::with('priorityTo.children.children')->find($id);
			return $matter->priorityTo->where('parent_id', null)->groupBy('caseref');
		});
});
