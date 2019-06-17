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
Route::any('/register', 'HomeController@index');

Route::get('/home', 'HomeController@index')->name('home');

Route::group(['middleware' => 'auth'], function () {
    // Matter Controller
    Route::get('matter/autocomplete', function (Request $request) {
        $term = $request->input('term');
        return App\Matter::with('filing')->selectRaw('id as `key`, CONCAT(caseref, suffix) as value')
                        ->where('caseref', 'like', "$term%")
                        ->take(10)->get();
    });
    Route::get('matter/export', 'MatterController@export');
    //Route::get('matter/{matter}', 'MatterController@show')->middleware('can:view-noclient'); // Breaks the Route::resource method
    Route::get('matter/{matter}/events', 'MatterController@events');
    Route::get('matter/{matter}/tasks', 'MatterController@tasks');
    Route::get('matter/{matter}/classifiers', 'MatterController@classifiers');
    Route::get('matter/{matter}/renewals', 'MatterController@renewals');
    Route::get('matter/{matter}/roleActors/{role}', 'MatterController@actors');
    Route::get('matter/{matter}/description/{lang}', 'MatterController@description');
    Route::get('matter/{from_matter}/createN', function (Matter $from_matter) {
        return view('matter.createN', compact('from_matter'));
    });
    Route::post('matter/storeN', 'MatterController@storeN');
    Route::post('matter/clear-tasks', 'HomeController@clearTasks');
    Route::post('matter/search', function (Request $request) {
        $matter_search = $request->input('matter_search');
        $option = $request->input('search_field');
        if ($option == "Ref") {
            $filter = array('Ref'  => $matter_search);
            $matters = Matter::filter('caseref',  'asc', $filter, false, true);
            if (count($matters) == 1) {
                return redirect('matter/' . $matters[0]->id);
            }
            return redirect('/matter?Ref='.$matter_search);
        }
        elseif ($option == "Responsible") {
            return redirect('/matter?responsible='.$matter_search);
        }
    });

    Route::get('matter/new-caseref', function (Request $request) {
        $term = $request->input('term');
        $newref = App\Matter::where('caseref', 'like', "$term%")->max('caseref');
        $newref++;
        return [['key' => 0, 'value' => $newref ]];
    });

    Route::get('event-name/autocomplete/{is_task}', function (Request $request, $is_task) {
        $term = $request->input('term');
        $results = App\EventName::select('name as value', 'code as key')
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
        $results = App\ClassifierType::select('type as value', 'code as key')
                ->where('type', 'like', "%$term%")
                ->where('main_display', $main_display)
                ->orderBy('type');
        return $results->take(10)->get();
    });

    Route::get('user/autocomplete', function (Request $request) {
        $term = $request->input('term');
        return App\User::select('name as value', 'login as key')
                        ->whereNotNull('login')
                        ->where('name', 'like', "%$term%")
                        ->take(10)->get();
    });

    Route::get('actor/autocomplete', function (Request $request) {
        $term = $request->input('term');
        $list = App\Actor::select('name as value', 'id as key', 'company_id')
                        ->where('name', 'like', "%$term%")
                        ->take(10)->get();
        if ( $list->count() == 0 ) {
          $list->push(['label' => 'Unknown. Create?', 'value' => 'create', 'company_id' => null]);
        }
        return $list;
    });

    Route::get('role/autocomplete', function (Request $request) {
        $term = $request->input('term');
        return App\Role::select('name as value', 'code as key', 'shareable')
                        ->where('name', 'like', "%$term%")->get();
    });

    Route::get('country/autocomplete', function (Request $request) {
        $term = $request->input('term');
        $list = App\Country::select('name as value', 'iso as key')
                        ->where('name', 'like', "$term%")->get();
        return $list;
    });

    Route::get('category/autocomplete', function (Request $request) {
        $term = $request->input('term');
        return App\Category::select('category as value', 'code as key')
                        ->where('category', 'like', "$term%")->get();
    });

    Route::get('type/autocomplete', function (Request $request) {
        $term = $request->input('term');
        return App\Type::select('type as value', 'code as key')
                        ->where('type', 'like', "$term%")->get();
    });

    /* DUPLICATE - use /event-name/autocomplete instead
    Route::get('event/autocomplete', function (Request $request) {
        $term = $request->input('term');
        return App\EventName::select('name as label', 'code as value')
                        ->where('name', 'like', "$term%")->get();
    });*/

    Route::resource('matter', 'MatterController');
    Route::apiResource('task', 'TaskController');
    Route::apiResource('event', 'EventController');
    Route::resource('actor', 'ActorController');
    Route::get('actor/{actor}/usedin','ActorPivotController@usedIn');
    Route::resource('eventname', 'EventNameController');
    Route::resource('rule', 'RuleController');
    Route::apiResource('actor-pivot', 'ActorPivotController');
    Route::apiResource('classifier', 'ClassifierController');

    // Testing - not used
    /* Route::get('matter/{matter}/actors', function (App\Matter $matter) {
      //$actors = $matter->with('container.actors.actor:id,name,display_name,company_id', 'actors.actor:id,name,display_name,company_id')->get();
      return $matter->actors;
      });

      Route::get('matter/{matter}/classifiers', function (App\Matter $matter) {
      return $matter->classifiers->where('main_display', 0);
      });

      Route::get('matter/{matter}/titles', function (App\Matter $matter) {
      return $matter->classifiers->where('main_display', 1);
      });

      Route::get('matter/{matter}/category', function (App\Matter $matter) {
      return $matter->category;
      });

      Route::get('matter/{matter}/type', function (App\Matter $matter) {
      return $matter->type;
      });

      Route::get('matter/{matter}/country', function (App\Matter $matter) {
      return $matter->countryInfo;
      });

      Route::get('matter/{matter}/origin', function (App\Matter $matter) {
      return $matter->originInfo;
      });

      Route::get('task/{task}/event', function (App\Task $task) {
      return $task->event;
      });

      Route::get('event/{event}/tasks', function (App\Event $event) {
      return $event->tasks;
      });

      Route::get('event/{event}/link', function (App\Event $event) {
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

      Route::get('matter/{matter}/container', function (App\Matter $matter) {
      return $matter->container;
      });

      Route::get('matter/{matter}/status', function (App\Matter $matter) {
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
      }); */
});
