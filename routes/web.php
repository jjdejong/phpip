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
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes(['register' => false]);

Route::get('/home', 'HomeController@index')->name('home');

Route::group(['middleware' => 'auth'], function () {
    // Matter Controller
    Route::get('matter/autocomplete', function (Request $request) {
        $term = $request->input('term');
        return App\Matter::with('filing')->select('id as key', 'uid as value')
                        ->where('uid', 'like', "$term%")
                        ->take(15)->get();
    });
    Route::get('matter/export', 'MatterController@export');
    Route::get('matter/{matter}/events', 'MatterController@events');
    Route::get('matter/{matter}/tasks', 'MatterController@tasks');
    Route::get('matter/{matter}/classifiers', 'MatterController@classifiers');
    Route::get('matter/{matter}/renewals', 'MatterController@renewals');
    Route::get('matter/{matter}/roleActors/{role}', 'MatterController@actors');
    Route::get('matter/{matter}/description/{lang}', 'MatterController@description');
    Route::get('matter/{parent_matter}/createN', function (Matter $parent_matter) {
        return view('matter.createN', compact('parent_matter'));
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
        }
        return redirect("/matter?$option=$matter_search");
    });

    Route::get('matter/new-caseref', function (Request $request) {
        $term = $request->term;
        $newref = App\Matter::where('caseref', 'like', "$term%")->max('caseref');
        if ($newref) {
          $newref++;
        } else {
          $newref = strtoupper($term);
        }
        return [['key' => $newref, 'value' => $newref ]];
    });

    Route::get('event-name/autocomplete/{is_task}', function (Request $request, $is_task) {
        $term = $request->term;
        $results = App\EventName::select('name as value', 'code as key')
                ->where([
                  ['name', 'like', "$term%"],
                  ['is_task', $is_task]
                ]);
        if ($request->filled('category')) {
          $results->whereRaw('ifnull(category, ?) = ?', [$request->category, $request->category]);
        }
        return $results->take(10)->get();
    });

    Route::get('classifier-type/autocomplete/{main_display}', function (Request $request, $main_display) {
        $term = $request->input('term');
        $results = App\ClassifierType::select('type as value', 'code as key')
                ->where('type', 'like', "$term%")
                ->where('main_display', $main_display)
                ->orderBy('type');
        return $results->take(10)->get();
    });

    Route::get('user/autocomplete', function (Request $request) {
        $term = $request->input('term');
        return App\User::select('name as value', 'login as key')
                        ->where('name', 'like', "$term%")
                        ->orWhere('login', 'like', "$term%")
                        ->take(10)->get();
    });

    Route::get('actor/autocomplete/{create_option?}', function (Request $request, $create_option = null) {
        $term = $request->input('term');
        $list = App\Actor::select('name as value', 'id as key')
                        ->where('name', 'like', "$term%")
                        ->take(10)->get();
        if ( $list->count() < 5 && $create_option ) {
          $list->push(['label' => 'Unknown. Create?', 'key' => 'create']);
        }
        return $list;
    });

    Route::get('role/autocomplete', function (Request $request) {
        $term = $request->input('term');
        return App\Role::select('name as value', 'code as key', 'shareable')
                        ->where('name', 'like', "$term%")->get();
    });

    Route::get('dbrole/autocomplete', function (Request $request) {
        $term = $request->input('term');
        return App\Role::select('name as value', 'code as key')
                        ->where('name', 'like', "$term%")
                        ->whereIn('code', ['CLI', 'DBA', 'DBRW', 'DBRO'])->get();
    });

    Route::get('country/autocomplete', function (Request $request) {
        $term = $request->input('term');
        $list = App\Country::select('name as value', 'iso as key')
                        ->where('name', 'like', "$term%")
                        ->orWhere('iso', 'like', "$term%")->get();
        return $list;
    });

    Route::get('category/autocomplete', function (Request $request) {
        $term = $request->input('term');
        return App\Category::select('category as value', 'code as key', 'ref_prefix as prefix')
                        ->where('category', 'like', "$term%")->get();
    });

    Route::get('type/autocomplete', function (Request $request) {
        $term = $request->input('term');
        return App\Type::select('type as value', 'code as key')
                        ->where('type', 'like', "$term%")->get();
    });

    Route::get('classifier/{classifier}/img', function ( App\Classifier $classifier) {
        return response($classifier->img)
            ->header('Content-Type', $classifier->value);
    });

    Route::resource('matter', 'MatterController');
    Route::apiResource('task', 'TaskController');
    Route::apiResource('event', 'EventController');
    Route::resource('category', 'CategoryController');
    Route::resource('classifier_type', 'ClassifierTypeController');
    Route::resource('role', 'RoleController');
    Route::resource('type', 'MatterTypeController');
    Route::resource('default_actor', 'DefaultActorController');
    Route::resource('actor', 'ActorController');
    Route::resource('user', 'UserController');
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
