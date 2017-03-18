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
	
	/*Route::get('matter/{matter}', function ($id) {
		$matter = App\Matter::with('tasksPending.info', 'renewalsPending', 'events.info', 'classifiers.type', 'container.classifiers.type')->find($id);
	    //return $matter;
	    return view('matter.view', compact('matter'));
	});*/

	Route::get('matter/{id}/events', function ($id) {
		$matter = Matter::with('events.info')->find($id);
	    return $matter->events;
	});

	Route::get('matter/{id}/tasks', function ($id) {
		$matter = Matter::with('tasks.info', 'tasks.trigger.info')->find($id);
	    return $matter->tasks->groupBy('trigger.info.name')->sortBy('trigger.event_date');
	});
	
	Route::get('matter/{id}/renewals', function ($id) {
		$matter = Matter::find($id);
		return $matter->renewals;
	});

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

	Route::get('actor/{id}', function ($id) {
		return App\Actor::find($id);
	});

	Route::get('actor/search/{term}', function ($term) {
		return App\Actor::where('name', 'like', "%$term%")->simplePaginate(25);
	});
	
	Route::get('role', function () {
		return App\Role::all();
	});
	
	// Testing
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
			$matter = App\Matter::find($id);
			return $matter->container;
		});
		
		Route::get('matter/{id}/linked_by', function ($id) {
			$matter = App\Matter::find($id);
			return $matter->linkedBy;
		});
});
