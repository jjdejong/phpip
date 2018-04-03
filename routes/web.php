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
use Illuminate\Http\Request;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

// Re-route registration requests to the home controller (thus disabling registration)
Route::any('/register','HomeController@index');

Route::get('/home', 'HomeController@index')->name('home');

Route::group(['middleware' => 'auth'], function () {
	// Matter Controller
	Route::get('matter/autocomplete', function (Request $request) {
		$term = $request->input('term');
		return App\Matter::with('filing')->selectRaw('id as value, CONCAT(caseref, suffix) as label')
		->where('caseref', 'like', "$term%")
		->take(10)->get();
	});
	Route::get('matter', 'MatterController@index');
	Route::get('matter/export', 'MatterController@export');
	Route::get('matter/{matter}', 'MatterController@show')->middleware('can:view-noclient');
	Route::get('matter/{matter}/events', 'MatterController@events');
	Route::get('matter/{matter}/tasks', 'MatterController@tasks');
	Route::get('matter/{matter}/renewals', 'MatterController@renewals');
	Route::put('matter/{matter}', 'MatterController@update');

	Route::get('event-name/autocomplete/{is_task}', function (Request $request, $is_task) {
		$term = $request->input('term');
		$results = App\EventName::select('name as value', 'code')
			->where('name', 'like', "%$term%")
			->where('is_task', $is_task);
		return $results->take(10)->get();
	});

	Route::get('task-name/autocomplete/{is_task}', function (Request $request, $is_task) {
		$term = $request->input('term');
		$results = App\EventName::select('name as label', 'code as value')
			->where('name', 'like', "%$term%")
			->where('is_task', $is_task);
		return $results->take(10)->get();
	});

	Route::get('classifier-type/autocomplete/{main_display}', function (Request $request, $main_display) {
		$term = $request->input('term');
		$results = App\ClassifierType::select('type as value', 'code')
			->where('type', 'like', "%$term%")
			->where('main_display', $main_display)
			->orderBy('type');
		return $results->take(10)->get();
	});

	Route::get('user/autocomplete', function (Request $request) {
		$term = $request->input('term');
		return App\User::select('name as label', 'login as value')
			->whereNotNull('login')
			->where('name', 'like', "%$term%")
			->take(10)->get();
	});

	Route::get('actor/autocomplete', function (Request $request) {
		$term = $request->input('term');
		return App\Actor::select('name as label', 'id as value', 'company_id')
			->where('name', 'like', "%$term%")
			->take(10)->get();
	});

	Route::get('role/autocomplete', function (Request $request) {
    $term = $request->input('term');
		return App\Role::select('name as label', 'code as value', 'shareable')
		->where('name', 'like', "%$term%")->get();
	});

	Route::get('country/autocomplete', function (Request $request) {
		$term = $request->input('term');
		$list = App\Country::select('name as label', 'iso as value')
		->where('name', 'like', "$term%")->get();
		return $list;
	});

	Route::get('category/autocomplete', function (Request $request) {
		$term = $request->input('term');
		return App\Category::select('category as label', 'code as value')
		->where('category', 'like', "$term%")->get();
	});

	Route::get('type/autocomplete', function (Request $request) {
		$term = $request->input('term');
		return App\Type::select('type as label', 'code as value')
		->where('type', 'like', "$term%")->get();
	});

	Route::get('event/autocomplete', function (Request $request) {
		$term = $request->input('term');
		return App\EventName::select('name as label', 'code as value')
		->where('name', 'like', "$term%")->get();
	});

	Route::get('rule','RuleController@index');
	Route::put('rule/{rule}/delete','RuleController@delete');
	Route::get('ruleinfo/{rule}','RuleController@show');
	Route::put('ruleinfo/{rule}','RuleController@update');
	Route::get('ruleadd','RuleController@addShow');
	Route::put('ruleadd','RuleController@store');

	Route::resource('task', 'TaskController');
	Route::resource('event', 'EventController');
	Route::resource('actor', 'ActorController');
	Route::resource('classifier', 'ClassifierController');

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
