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

use App\Models\Matter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MatterController;
use App\Http\Controllers\RenewalController;
use App\Http\Controllers\DocumentController;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes(['register' => false]);

Route::get('/home', [HomeController::class, 'index'])->name('home');

Route::middleware(['auth'])->group(function () {
    Route::get('matter/autocomplete', function (Request $request) {
        $term = $request->input('term');

        return App\Models\Matter::with('filing')->select('id as key', 'uid as value')
            ->where('uid', 'like', "$term%")
            ->take(15)->get();
    });
    Route::controller(MatterController::class)->group(function () {
        Route::get('matter/export', 'export');
        Route::post('matter/{matter}/mergeFile', 'mergeFile');
        Route::get('matter/{matter}/events', 'events');
        Route::get('matter/{matter}/tasks', 'tasks');
        Route::get('matter/{matter}/classifiers', 'classifiers');
        Route::get('matter/{matter}/renewals', 'renewals');
        Route::get('matter/{matter}/roleActors/{role}', 'actors');
        Route::get('matter/{matter}/description/{lang}', 'description');
        Route::get('matter/{matter}/info', 'info');
        Route::post('matter/storeN', 'storeN');
        Route::get('matter/getOPSfamily/{docnum}', 'getOPSfamily');
        Route::post('matter/storeFamily', 'storeFamily');
    });

    Route::controller(RenewalController::class)->group(function () {
        Route::post('renewal/order', 'renewalOrder');
        Route::post('renewal/call/{send}', 'firstcall');
        Route::post('renewal/reminder', 'remindercall');
        Route::post('renewal/invoice/{toinvoice}', 'invoice');
        Route::post('renewal/topay', 'topay');
        Route::post('renewal/paid', 'paid');
        Route::post('renewal/done', 'done');
        Route::post('renewal/lastcall', 'lastcall');
        Route::post('renewal/receipt', 'receipt');
        Route::post('renewal/closing', 'closing');
        Route::post('renewal/abandon', 'abandon');
        Route::post('renewal/lapsing', 'lapsing');
        Route::get('renewal/export', 'export');
        Route::get('logs', 'logs');
    });

    Route::controller(App\Http\Controllers\DocumentController::class)->group(function () {
        Route::post('document/mailto/{member}', 'mailto');
        Route::get('document/select/{matter}', 'select');
    });

    Route::post('matter/search', function (Request $request) {
        $matter_search = $request->input('matter_search');
        $option = $request->input('search_field');
        if ($option == 'Ref') {
            $filter = ['Ref' => $matter_search];
            $matters = Matter::filter('caseref', 'asc', $filter, false, true)->get();
            if (count($matters) == 1) {
                return redirect('matter/' . $matters[0]->id);
            }
        }

        return redirect("/matter?$option=$matter_search");
    });

    Route::get('matter/new-caseref', function (Request $request) {
        $term = $request->term;
        $newref = App\Models\Matter::where('caseref', 'like', "$term%")->max('caseref');
        if ($newref && $newref != $term) {
            $newref++;
        } else {
            $newref = strtoupper($term);
        }

        return [['key' => $newref, 'value' => $newref]];
    });

    Route::get('classifier/{classifier}/img', fn (App\Models\Classifier $classifier) => response($classifier->img)
        ->header('Content-Type', $classifier->value));

    // Autocompletions (accessible only to read-write users)
    Route::middleware('can:readwrite')->group(function () {

        Route::get('event-name/autocomplete/{is_task}', function (Request $request, $is_task) {
            $term = $request->term;
            $results = App\Models\EventName::select('name as value', 'code as key')
                ->where([
                    ['name', 'like', "$term%"],
                    ['is_task', $is_task],
                ]);
            if ($request->filled('category')) {
                $results->whereRaw('ifnull(category, ?) = ?', [$request->category, $request->category]);
            }

            return $results->take(10)->get();
        });

        Route::get('classifier-type/autocomplete/{main_display}', function (Request $request, $main_display) {
            $term = $request->input('term');
            $results = App\Models\ClassifierType::select('type as value', 'code as key')
                ->where('type', 'like', "$term%")
                ->where('main_display', $main_display)
                ->orderBy('type');

            return $results->take(10)->get();
        });

        Route::get('user/autocomplete', function (Request $request) {
            $term = $request->input('term');

            return App\Models\User::select('name as value', 'login as key')
                ->where('name', 'like', "$term%")
                ->orWhere('login', 'like', "$term%")
                ->take(10)->get();
        });

        Route::get('actor/autocomplete/{create_option?}', function (Request $request, $create_option = null) {
            $term = $request->input('term');
            $list = App\Models\Actor::select(DB::raw('coalesce(display_name, name) as value'), 'id as key')
                ->where('name', 'like', "$term%")
                ->orWhere('display_name', 'like', "$term")
                ->take(10)->get();
            if ($list->count() < 5 && $create_option) {
                $list->push(['label' => "Create $term?", 'key' => 'create', 'value' => $term]);
            }

            return $list;
        });

        Route::get('role/autocomplete', function (Request $request) {
            $term = $request->input('term');

            return App\Models\Role::select('name as value', 'code as key', 'shareable')
                ->where('name', 'like', "$term%")
                ->orWhere('code', 'like', "$term%")->get();
        });

        Route::get('dbrole/autocomplete', function (Request $request) {
            $term = $request->input('term');

            return App\Models\Role::select('name as value', 'code as key')
                ->where('name', 'like', "$term%")
                ->whereIn('code', ['CLI', 'DBA', 'DBRW', 'DBRO'])->get();
        });

        Route::get('country/autocomplete', function (Request $request) {
            $term = $request->input('term');
            $list = App\Models\Country::select('name as value', 'iso as key')
                ->where('name', 'like', "$term%")
                ->orWhere('iso', 'like', "$term%")->get();

            return $list;
        });

        Route::get('category/autocomplete', function (Request $request) {
            $term = $request->input('term');

            return App\Models\Category::select('category as value', 'code as key', 'ref_prefix as prefix')
                ->where('category', 'like', "$term%")
                ->orWhere('code', 'like', "$term%")->get();
        });

        Route::get('type/autocomplete', function (Request $request) {
            $term = $request->input('term');

            return App\Models\Type::select('type as value', 'code as key')
                ->where('type', 'like', "$term%")
                ->orWhere('code', 'like', "$term%")->get();
        });

        Route::get('template-category/autocomplete', function (Request $request) {
            $term = $request->input('term');
            $list = App\Models\TemplateMember::select('category as value', 'category as key')
                ->where('category', 'like', "$term%")->distinct()->get();
            if ($list->count() == 0) {
                $list->push(['label' => "Create $term", 'key' => $term, 'value' => $term]);
            }

            return $list;
        });

        Route::get('template-class/autocomplete', function (Request $request) {
            $term = $request->input('term');

            return App\Models\TemplateClass::select('name as value', 'id as key')
                ->where('name', 'like', "$term%")->get();
        });

        Route::get('template-style/autocomplete', function (Request $request) {
            $term = $request->input('term');
            $list = App\Models\TemplateMember::select('style as value', 'style as key')
                ->where('style', 'like', "$term%")->distinct()->get();
            if ($list->count() == 0) {
                $list->push(['label' => "Create $term", 'key' => $term, 'value' => $term]);
            }

            return $list;
        });
    });

    Route::post('event/{event}/recreateTasks', fn (App\Models\Event $event) => DB::statement('CALL recreate_tasks(?, ?)', [$event->id, Auth::user()->login]));

    Route::resource('matter', MatterController::class);
    Route::resource('actor', App\Http\Controllers\ActorController::class);
    Route::resource('user', App\Http\Controllers\UserController::class);
    Route::apiResource('task', App\Http\Controllers\TaskController::class);

    // The following resources are not accessible to clients
    Route::middleware('can:except_client')->group(function () {
        Route::post('matter/clear-tasks', [HomeController::class, 'clearTasks']);
        Route::get('matter/{parent_matter}/createN', fn (Matter $parent_matter) => view('matter.createN', compact('parent_matter')));
        Route::apiResource('event', App\Http\Controllers\EventController::class);
        Route::resource('category', App\Http\Controllers\CategoryController::class);
        Route::resource('classifier_type', App\Http\Controllers\ClassifierTypeController::class);
        Route::resource('role', App\Http\Controllers\RoleController::class);
        Route::resource('type', App\Http\Controllers\MatterTypeController::class);
        Route::resource('default_actor', App\Http\Controllers\DefaultActorController::class);
        Route::get('actor/{actor}/usedin', [App\Http\Controllers\ActorPivotController::class, 'usedIn']);
        Route::resource('eventname', App\Http\Controllers\EventNameController::class);
        Route::resource('rule', App\Http\Controllers\RuleController::class);
        Route::apiResource('actor-pivot', App\Http\Controllers\ActorPivotController::class);
        Route::apiResource('classifier', App\Http\Controllers\ClassifierController::class);
        Route::resource('renewal', RenewalController::class);
        Route::resource('fee', App\Http\Controllers\FeeController::class);
        Route::resource('template-member', App\Http\Controllers\TemplateMemberController::class);
        Route::resource('document', DocumentController::class)->parameters(['document' => 'class']);
        Route::resource('event-class', App\Http\Controllers\EventClassController::class);
    });
});
