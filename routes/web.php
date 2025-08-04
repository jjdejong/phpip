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
use App\Http\Controllers\CountryController;

// Countries management routes (DBA only)
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/countries', [CountryController::class, 'index'])->name('countries.index');
    Route::get('/countries/create', [CountryController::class, 'create'])->name('countries.create');
    Route::post('/countries', [CountryController::class, 'store'])->name('countries.store');
    Route::get('/countries/{country}', [CountryController::class, 'show'])->name('countries.show');
    Route::put('/countries/{country}', [CountryController::class, 'update'])->name('countries.update');
    Route::delete('/countries/{country}', [CountryController::class, 'destroy'])->name('countries.destroy');
});
use App\Http\Controllers\RenewalController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\AutocompleteController;
use App\Http\Controllers\MatterSearchController;
use App\Http\Controllers\ClassifierController;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes(['register' => false]);

Route::get('/home', [HomeController::class, 'index'])->name('home');

Route::middleware(['auth'])->group(function () {
    // Matter routes group
    Route::controller(MatterController::class)->prefix('matter')->name('matter.')->group(function () {
        Route::get('autocomplete', [AutocompleteController::class, 'matter'])->name('autocomplete');
        Route::get('new-caseref', [AutocompleteController::class, 'newCaseref'])->name('new-caseref');
        Route::post('search', [MatterSearchController::class, 'search'])->name('search');
        Route::get('export', 'export')->name('export');
        Route::post('{matter}/mergeFile', 'mergeFile')->name('mergeFile');
        Route::get('{matter}/events', 'events')->name('events');
        Route::get('{matter}/tasks', 'tasks')->name('tasks');
        Route::get('{matter}/classifiers', 'classifiers')->name('classifiers');
        Route::get('{matter}/renewals', 'renewals')->name('renewals');
        Route::get('{matter}/roleActors/{role}', 'actors')->name('actors');
        Route::get('{matter}/description/{lang}', 'description')->name('description');
        Route::get('{matter}/info', 'info')->name('info');
        Route::post('storeN', 'storeN')->name('storeN');
        Route::get('getOPSfamily/{docnum}', 'getOPSfamily')->name('getOPSfamily');
        Route::post('storeFamily', 'storeFamily')->name('storeFamily');
    });

    // Autocomplete routes - protected by readwrite middleware
    Route::middleware('can:readwrite')->group(function () {
        Route::get('event-name/autocomplete/{is_task}', [AutocompleteController::class, 'eventName']);
        Route::get('classifier-type/autocomplete/{main_display}', [AutocompleteController::class, 'classifierType']);
        Route::get('user/autocomplete', [AutocompleteController::class, 'user']);
        Route::get('actor/autocomplete/{create_option?}', [AutocompleteController::class, 'actor']);
        Route::get('role/autocomplete', [AutocompleteController::class, 'role']);
        Route::get('dbrole/autocomplete', [AutocompleteController::class, 'dbrole']);
        Route::get('country/autocomplete', [AutocompleteController::class, 'country']);
        Route::get('category/autocomplete', [AutocompleteController::class, 'category']);
        Route::get('type/autocomplete', [AutocompleteController::class, 'type']);
        Route::get('template-category/autocomplete', [AutocompleteController::class, 'templateCategory']);
        Route::get('template-class/autocomplete', [AutocompleteController::class, 'templateClass']);
        Route::get('template-style/autocomplete', [AutocompleteController::class, 'templateStyle']);
    });

    // Classifier routes
    Route::get('classifier/{classifier}/img', [ClassifierController::class, 'showImage'])->name('classifier.image');

    // Renewal routes
    Route::controller(RenewalController::class)->prefix('renewal')->name('renewal.')->group(function () {
        Route::post('order', 'renewalOrder');
        Route::post('call/{send}', 'firstcall');
        Route::post('reminder', 'remindercall');
        Route::post('invoice/{toinvoice}', 'invoice');
        Route::post('topay', 'topay');
        Route::post('paid', 'paid');
        Route::post('done', 'done');
        Route::post('lastcall', 'lastcall');
        Route::post('receipt', 'receipt');
        Route::post('closing', 'closing');
        Route::post('abandon', 'abandon');
        Route::post('lapsing', 'lapsing');
        Route::get('export', 'export');
        Route::get('logs', 'logs');
    });

    // Document routes
    Route::controller(DocumentController::class)->prefix('document')->name('document.')->group(function () {
        Route::post('mailto/{member}', 'mailto');
        Route::get('select/{matter}', 'select');
    });

    Route::post('event/{event}/recreateTasks', fn (App\Models\Event $event) => DB::statement('CALL recreate_tasks(?, ?)', [$event->id, Auth::user()->login]));

    // Resource routes
    Route::resource('matter', MatterController::class);
    Route::resource('actor', App\Http\Controllers\ActorController::class);
    Route::resource('user', App\Http\Controllers\UserController::class);
    Route::get('/profile', [App\Http\Controllers\UserController::class, 'profile'])->name('user.profile');
    Route::put('/profile/update', [App\Http\Controllers\UserController::class, 'updateProfile'])->name('user.updateProfile');
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
