<?php

namespace App\Http\Controllers;

use App\Http\Requests\MatterExportRequest;
use App\Http\Requests\MergeFileRequest;
use App\Models\Actor;
use App\Models\ActorPivot;
use App\Models\Event;
use App\Models\Matter;
use App\Services\DocumentMergeService;
use App\Services\MatterExportService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use SimpleXMLElement;

class MatterController extends Controller
{
    protected DocumentMergeService $documentMergeService;
    protected MatterExportService $matterExportService;

    public function __construct(
        DocumentMergeService $documentMergeService,
        MatterExportService  $matterExportService
    )
    {
        $this->documentMergeService = $documentMergeService;
        $this->matterExportService = $matterExportService;
    }

    public function index(Request $request)
    {
        $filters = $request->except(
            [
                'display_with',
                'page',
                'filter',
                'value',
                'sortkey',
                'sortdir',
                'tab',
                'include_dead',
            ]
        );

        $matters = Matter::filter(
            $request->input('sortkey', 'id'),
            $request->input('sortdir', 'desc'),
            $filters,
            $request->display_with,
            $request->include_dead
        )->simplePaginate(25);
        $matters->withQueryString()->links(); // Keep URL parameters in the paginator links

        return view('matter.index', compact('matters'));
    }

    public function show(Matter $matter)
    {
        $this->authorize('view', $matter);
        $matter->load(['tasksPending.info', 'renewalsPending', 'events.info', 'titles', 'actors', 'classifiers']);

        return view('matter.show', compact('matter'));
    }

    /**
     * Return a JSON array with info of a matter. For use with API REST.
     *
     * @param int $id
     * @return Json
     **/
    public function info($id)
    {
        return Matter::with(
            ['tasksPending.info', 'renewalsPending', 'events.info', 'titles', 'actors', 'classifiers']
        )->find($id);
    }

    public function create(Request $request)
    {
        Gate::authorize('readwrite');
        $operation = $request->input('operation', 'new'); // new, clone, child, ops
        $category = [];
        $category_code = $request->input('category', 'PAT');
        if ($operation != 'new' && $operation != 'ops') {
            $parent_matter = Matter::with(
                'container',
                'countryInfo',
                'originInfo',
                'category',
                'type'
            )->find($request->matter_id);
            if ($operation == 'clone') {
                // Generate the next available caseref based on the prefix
                $parent_matter->caseref = Matter::where(
                    'caseref',
                    'like',
                    $parent_matter->category->ref_prefix . '%'
                )->max('caseref');
                $parent_matter->caseref++;
            }
        } else {
            $parent_matter = new Matter; // Create empty matter object to avoid undefined errors in view
            $ref_prefix = \App\Models\Category::find($category_code)['ref_prefix'];
            $category = [
                'code' => $category_code,
                'next_caseref' => Matter::where('caseref', 'like', $ref_prefix . '%')
                    ->max('caseref'),
                'name' => \App\Models\Category::find($category_code)['category'],
            ];
            $category['next_caseref']++;
        }

        return view('matter.create', compact('parent_matter', 'operation', 'category'));
    }

    public function store(Request $request)
    {
        Gate::authorize('readwrite');
        $this->validate(
            $request,
            [
                'category_code' => 'required',
                'caseref' => 'required',
                'country' => 'required',
                'responsible' => 'required',
                'expire_date' => 'date',
            ]
        );

        // Unique UID handling
        $matters = Matter::where(
            [
                ['caseref', $request->caseref],
                ['country', $request->country],
                ['category_code', $request->category_code],
                ['origin', $request->origin],
                ['type_code', $request->type_code],
            ]
        );

        $request->merge(['creator' => Auth::user()->login]);

        $idx = $matters->count();

        if ($idx > 0) {
            $request->merge(['idx' => $idx + 1]);
        }

        $new_matter = Matter::create($request->except(['_token', '_method', 'operation', 'parent_id', 'priority']));

        switch ($request->operation) {
            case 'child':
                $parent_matter = Matter::with('priority', 'filing')->find($request->parent_id);
                // Copy priority claims from original matter
                $new_matter->priority()->createMany($parent_matter->priority->toArray());
                $new_matter->container_id = $parent_matter->container_id ?? $request->parent_id;
                if ($request->priority) {
                    $new_matter->events()->create(['code' => 'PRI', 'alt_matter_id' => $request->parent_id]);
                } else {
                    // Copy Filing event from original matter
                    $new_matter->filing()->save($parent_matter->filing->replicate(['detail']));
                    $new_matter->parent_id = $request->parent_id;
                    $new_matter->events()->create(
                        [
                            'code' => 'ENT',
                            'event_date' => now(),
                            'detail' => 'Child filing date',
                        ]
                    );
                }
                $new_matter->save();
                break;
            case 'clone':
                $parent_matter = Matter::with('priority', 'classifiersNative', 'actorPivot')->find($request->parent_id);
                // Copy priority claims from original matter
                $new_matter->priority()->createMany($parent_matter->priority->toArray());
                // Copy actors from original matter
                // Cannot use Eloquent relationships because they do not handle unique key constraints
                // - the issue arises for actors that are inserted upon matter creation by a trigger based
                //   on the default_actors table
                $actors = $parent_matter->actorPivot;
                $new_matter_id = $new_matter->id;
                $actors->each(function ($item) use ($new_matter_id) {
                    $item->matter_id = $new_matter_id;
                    $item->id = null;
                });
                ActorPivot::insertOrIgnore($actors->toArray());
                if ($parent_matter->container_id) {
                    // Copy shared actors and classifiers from original matter's container
                    $actors = $parent_matter->container->actorPivot->where('shared', 1);
                    $actors->each(function ($item) use ($new_matter_id) {
                        $item->matter_id = $new_matter_id;
                        $item->id = null;
                    });
                    ActorPivot::insertOrIgnore($actors->toArray());
                    $new_matter->classifiersNative()
                        ->createMany($parent_matter->container->classifiersNative->toArray());
                } else {
                    // Copy classifiers from original matter
                    $new_matter->classifiersNative()->createMany($parent_matter->classifiersNative->toArray());
                }
                break;
            case 'new':
                $new_matter->events()->create(['code' => 'REC', 'event_date' => now()]);
                break;
        }

        return response()->json(['redirect' => route('matter.show', [$new_matter])]);
    }

    public function storeN(Request $request)
    {
        Gate::authorize('readwrite');
        $this->validate(
            $request,
            ['ncountry' => 'required|array']
        );

        $parent_id = $request->parent_id;
        $parent_matter = Matter::with('priority', 'filing', 'publication', 'grant', 'classifiersNative')
            ->find($parent_id);

        foreach ($request->ncountry as $country) {
            $request->merge(
                [
                    'country' => $country,
                    'creator' => Auth::user()->login,
                ]
            );

            $new_matter = Matter::create($request->except(['_token', '_method', 'ncountry', 'parent_id']));

            // Copy shared events from original matter
            $new_matter->priority()->createMany($parent_matter->priority->toArray());
            //$new_matter->parentFiling()->createMany($parent_matter->parentFiling->toArray());
            $new_matter->filing()->save($parent_matter->filing->replicate());
            if ($parent_matter->publication()->exists()) {
                $new_matter->publication()->save($parent_matter->publication->replicate());
            }
            if ($parent_matter->grant()->exists()) {
                $new_matter->grant()->save($parent_matter->grant->replicate());
            }

            // Insert "entered" event tracing the actual date of the step
            $new_matter->events()->create(['code' => 'ENT', 'event_date' => now()]);
            // Insert "Parent filed" event tracing the filing number of the parent PCT or EP
            $new_matter->events()->create(['code' => 'PFIL', 'alt_matter_id' => $request->parent_id]);

            $new_matter->parent_id = $parent_id;
            $new_matter->container_id = $parent_matter->container_id ?? $parent_id;
            $new_matter->save();
        }

        return response()->json(['redirect' => "/matter?Ref=$request->caseref&origin=$parent_matter->country"]);
    }

    public function storeFamily(Request $request)
    {
        Gate::authorize('readwrite');
        $this->validate($request, [
            'docnum' => 'required',
            'caseref' => 'required',
            'category_code' => 'required',
            'client_id' => 'required',
        ]);

        $apps = collect($this->getOPSfamily($request->docnum));
        if ($apps->has('errors') || $apps->has('exception')) {
            return response()->json($apps);
        }
        $container = [];
        $container_id = null;
        $matter_id_num = [];
        $existing_fam = Matter::where('caseref', $request->caseref)->get();
        if ($existing_fam->count()) {
            $container = $existing_fam->where('container_id', null)->first();
            $container_id = $container->id;
            foreach ($existing_fam as $existing_app) {
                $matter_id_num[$existing_app->filing->cleanNumber()] = $existing_app->id;
            }
        }
        foreach ($apps as $key => $app) {
            if (array_key_exists($app['app']['number'], $matter_id_num)) {
                // Member exists, do not create
                continue;
            }
            $request->merge(
                [
                    'country' => $app['app']['country'],
                    'creator' => Auth::user()->login,
                ]
            );
            // Remove if set from a previous iteration
            $request->request->remove('type_code');
            $request->request->remove('origin');
            $request->request->remove('idx');
            if ($app['app']['kind'] == 'P') {
                $request->merge(['type_code' => 'PRO']);
            }
            if ($app['pct'] != null) {
                $request->merge(['origin' => 'WO']);
            }
            $parent_num = '';
            if ($app['div'] != null) {
                $request->merge(['type_code' => 'DIV']);
                $parent_num = $app['div'];
            }
            if ($app['cnt'] != null) {
                $request->merge(['type_code' => 'CNT']);
                $parent_num = $app['cnt'];
            }

            // Unique UID handling
            $matters = Matter::where(
                [
                    ['caseref', $request->caseref],
                    ['country', $request->country],
                    ['category_code', $request->category_code],
                    ['origin', $request->origin],
                    ['type_code', $request->type_code],
                ]
            );

            $idx = $matters->count();

            if ($idx > 0) {
                $request->merge(['idx' => $idx + 1]);
            }

            $new_matter = Matter::create($request->except(['_token', '_method', 'docnum', 'client_id']));
            $matter_id_num[$app['app']['number']] = $new_matter->id;

            if ($key == 0) {
                $container_id = $new_matter->id;
                foreach ($app['pri'] as $pri) {
                    // Create priority filings that refer to applications not returned by OPS (US provisionals)
                    if ($pri['number'] != $app['app']['number']) {
                        $new_matter->events()->create(
                            [
                                'code' => 'PRI',
                                'detail' => $pri['country'] . $pri['number'],
                                'event_date' => $pri['date'],
                            ]
                        );
                    }
                }
                $new_matter->classifiersNative()->create(['type_code' => 'TIT', 'value' => $app['title']]);
                $new_matter->actorPivot()->create(['actor_id' => $request->client_id, 'role' => 'CLI', 'shared' => 1]);
                if (strtolower($app['applicants'][0]) == strtolower(Actor::find($request->client_id)->name)) {
                    $new_matter->actorPivot()->create(
                        [
                            'actor_id' => $request->client_id,
                            'role' => 'APP',
                            'shared' => 1,
                        ]
                    );
                }
                foreach ($app['applicants'] as $applicant) {
                    // Search for phonetically equivalent in the actor table, and take first
                    if (substr($applicant, -1) == ',') {
                        // Remove ending comma
                        $applicant = substr($applicant, 0, -1);
                    }
                    if ($actor = Actor::whereRaw("name SOUNDS LIKE '$applicant'")->first()) {
                        // Some applicants are listed twice, with and without accents, so ignore unique key error for a second attempt
                        $new_matter->actorPivot()->firstOrCreate(
                            [
                                'actor_id' => $actor->id,
                                'role' => 'APP',
                                'shared' => 1,
                            ]
                        );
                    } else {
                        $new_actor = Actor::create(
                            [
                                'name' => $applicant,
                                'default_role' => 'APP',
                                'phy_person' => 0,
                                'notes' => "Inserted by OPS family create tool for matter ID $new_matter->id",
                            ]
                        );
                        $new_matter->actorPivot()->firstOrCreate(
                            [
                                'actor_id' => $new_actor->id,
                                'role' => 'APP',
                                'shared' => 1,
                            ]
                        );
                    }
                }
                foreach ($app['inventors'] as $inventor) {
                    // Search for phonetically equivalent in the actor table, and take first
                    if (substr($inventor, -1) == ',') {
                        // Remove ending comma
                        $inventor = substr($inventor, 0, -1);
                    }
                    if ($actor = Actor::whereRaw("name SOUNDS LIKE '$inventor'")->first()) {
                        // Some inventors are listed twice, with and without accents, so ignore second attempt
                        $new_matter->actorPivot()->firstOrCreate(
                            [
                                'actor_id' => $actor->id,
                                'role' => 'INV',
                                'shared' => 1,
                            ]
                        );
                    } else {
                        $new_actor = Actor::create(
                            [
                                'name' => $inventor,
                                'default_role' => 'INV',
                                'phy_person' => 1,
                                'notes' => "Inserted by OPS family create tool for matter ID $new_matter->id",
                            ]
                        );
                        $new_matter->actorPivot()->firstOrCreate(
                            [
                                'actor_id' => $new_actor->id,
                                'role' => 'INV',
                                'shared' => 1,
                            ]
                        );
                    }
                }
                $new_matter->notes = 'Applicants: ' . collect($app['applicants'])->implode('; ') . "\nInventors: " . collect($app['inventors'])->implode(' - ');
            } else {
                $new_matter->container_id = $container_id;
                foreach ($app['pri'] as $pri) {
                    // Create priority filings, excluding "auto" priority claim
                    if ($pri['number'] != $app['app']['number']) {
                        if (array_key_exists($pri['number'], $matter_id_num)) {
                            // The priority application is in the family
                            $new_matter->events()->create(
                                [
                                    'code' => 'PRI',
                                    'alt_matter_id' => $matter_id_num[$pri['number']]
                                ]
                            );
                        } else {
                            $new_matter->events()->create(
                                [
                                    'code' => 'PRI',
                                    'detail' => $pri['country'] . $pri['number'],
                                    'event_date' => $pri['date'],
                                ]
                            );
                        }
                    }
                }
            }
            if ($app['pct'] != null) {
                $new_matter->parent_id = $matter_id_num[$app['pct']];
                $new_matter->events()->create(
                    [
                        'code' => 'PFIL',
                        'alt_matter_id' => $new_matter->parent_id
                    ]
                );
            }
            if ($parent_num) {
                // This app is a divisional or a continuation
                $new_matter->events()->create(
                    [
                        'code' => 'ENT',
                        'event_date' => $app['app']['date'],
                        'detail' => 'Child filing date'
                    ]
                );
                $parent = $apps->where('app.number', $parent_num)->first();
                // Change this app's filing date to the parent's filing date for potential children of this app
                $app['app']['date'] = $parent['app']['date'];
                $new_matter->parent_id = $matter_id_num["$parent_num"];
            }
            $new_matter->events()->create(['code' => 'FIL', 'event_date' => $app['app']['date'], 'detail' => $app['app']['number']]);
            if (array_key_exists('pub', $app)) {
                $new_matter->events()->create(
                    [
                        'code' => 'PUB',
                        'event_date' => $app['pub']['date'],
                        'detail' => $app['pub']['number']
                    ]
                );
            }
            if (array_key_exists('grt', $app)) {
                $new_matter->events()->create(
                    [
                        'code' => 'GRT',
                        'event_date' => $app['grt']['date'],
                        'detail' => $app['grt']['number']
                    ]
                );
            }
            if (array_key_exists('procedure', $app)) {
                foreach ($app['procedure'] as $step) {
                    switch ($step['code']) {
                        case 'EXRE':
                            // Exam report
                            $exa = $new_matter->events()->create(['code' => 'EXA', 'event_date' => $step['dispatched']]);
                            if (array_key_exists('replied', $step) && $exa->event_date < now()->subMonths(4)) {
                                $exa->tasks()->create(
                                    [
                                        'code' => 'REP',
                                        'due_date' => $exa->event_date->addMonths(4),
                                        'done_date' => $step['replied'],
                                        'done' => 1,
                                        'detail' => 'Exam Report'
                                    ]
                                );
                            }
                            break;
                        case 'RFEE':
                            // Renewals
                            $new_matter->filing->tasks()->updateOrCreate(
                                ['code' => 'REN', 'detail' => $step['ren_year']],
                                [
                                    'due_date' => $new_matter->filing->event_date->addYears($step['ren_year'] - 1)->lastOfMonth(),
                                    'done_date' => $step['ren_paid'],
                                    'done' => 1
                                ]
                            );
                            break;
                        case 'IGRA':
                            // Intention to grant
                            if (array_key_exists('dispatched', $step)) {
                                // Sometimes the dispatch and the payment are in different steps
                                $grt = $new_matter->events()->create(['code' => 'ALL', 'event_date' => $step['dispatched']]);
                            }
                            if (array_key_exists('grt_paid', $step) && $grt->event_date < now()->subMonths(4)) {
                                $grt->tasks()->create(
                                    [
                                        'code' => 'PAY',
                                        'due_date' => $grt->event_date->addMonths(4),
                                        'done_date' => $step['grt_paid'],
                                        'done' => 1,
                                        'detail' => 'Grant Fee'
                                    ]
                                );
                            }
                            break;
                        case 'EXAM52':
                            // Filing request (useful for divisional actual filing date)
                            if ($new_matter->type_code == 'DIV') {
                                $new_matter->events->where('code', 'ENT')->first()->event_date = $step['request'];
                            }
                            break;
                    }
                }
            }
            // The push() method saves the model with all its relationships
            $new_matter->push();
        }

        return response()->json(['redirect' => "/matter?Ref=$request->caseref&tab=1"]);
    }

    public function edit(Matter $matter)
    {
        Gate::authorize('readwrite');
        $matter->load(
            'container',
            'parent',
            'countryInfo:iso,name',
            'originInfo:iso,name',
            'category',
            'type',
            'filing'
        );
        $country_edit = $matter->tasks()->whereHas(
                'rule',
                function (Builder $q) {
                    $q->whereNotNull('for_country');
                }
            )->count() == 0;
        $cat_edit = $matter->tasks()->whereHas(
                'rule',
                function (Builder $q) {
                    $q->whereNotNull('for_category');
                }
            )->count() == 0;

        return view('matter.edit', compact(['matter', 'cat_edit', 'country_edit']));
    }

    public function update(Request $request, Matter $matter)
    {
        Gate::authorize('readwrite');
        $request->validate(
            [
                'term_adjust' => 'numeric',
                'idx' => 'numeric|nullable',
                'expire_date' => 'date',
            ]
        );
        $request->merge(['updater' => Auth::user()->login]);
        $matter->update($request->except(['_token', '_method']));

        return $matter;
    }

    public function destroy(Matter $matter)
    {
        Gate::authorize('readwrite');
        $matter->delete();

        return $matter;
    }

    /**
     * Exports Matters list.
     *
     * This method exports a list of matters based on the provided filters and returns
     * a streamed response for downloading the file in CSV format.
     *
     * @param MatterExportRequest $request The request object containing the filters for exporting matters.
     * @return \Symfony\Component\HttpFoundation\StreamedResponse The streamed response for the CSV file download.
     */
    public function export(MatterExportRequest $request)
    {
        // Extract filters from the request, excluding certain parameters.
        $filters = $request->except(
            [
                'display_with',
                'page',
                'filter',
                'value',
                'sortkey',
                'sortdir',
                'tab',
                'include_dead',
            ]
        );

        // @TODO rewrite the filter method to use the new query builder
        // Retrieve the filtered matters and convert them to an array.
        $export = Matter::filter(
            $request->input('sortkey', 'caseref'),
            $request->input('sortdir', 'asc'),
            $filters,
            $request->display_with,
            $request->include_dead
        )->get()->toArray();

        // Export the matters array to a CSV file and return the streamed response.
        return $this->matterExportService->export($export);
    }

    /**
     * Generate merged document on the fly from uploaded template
     */
    public function mergeFile(Matter $matter, MergeFileRequest $request)
    {
        $file = $request->file('file');
        $template = $this->documentMergeService
            ->setMatter($matter)
            ->merge($file->path());

        return response()->streamDownload(function () use ($template) {
            $template->saveAs('php://output');
        }, 'merged-' . $file->getClientOriginalName(), [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'Content-Transfer-Encoding' => 'binary',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ]);
    }

    public function events(Matter $matter)
    {
        $events = $matter->events->load('info');

        return view('matter.events', compact('events', 'matter'));
    }

    public function tasks(Matter $matter)
    {
        // All events and their tasks, excepting renewals
        $events = Event::with(['tasks' => function ($query) {
            $query->where('code', '!=', 'REN');
        }, 'info:code,name', 'tasks.info:code,name'])->where('matter_id', $matter->id)
            ->orderBy('event_date')->get();
        $is_renewals = 0;

        return view('matter.tasks', compact('events', 'matter', 'is_renewals'));
    }

    public function renewals(Matter $matter)
    {
        // The renewal trigger event and its renewals
        $events = Event::with(
            [
                'tasks' => function ($query) {
                    $query->where('code', 'REN');
                }
            ]
        )->whereHas(
            'tasks',
            function ($query) {
                $query->where('code', 'REN');
            }
        )->where('matter_id', $matter->id)->get();
        $is_renewals = 1;

        return view('matter.tasks', compact('events', 'matter', 'is_renewals'));
    }

    public function actors(Matter $matter, $role)
    {
        $role_group = $matter->actors->where('role_code', $role);

        return view('matter.roleActors', compact('role_group', 'matter'));
    }

    public function classifiers(Matter $matter)
    {
        $matter->load(['classifiers']);

        return view('matter.classifiers', compact('matter'));
    }

    public function description(Matter $matter, $lang)
    {
        $description = $matter->getDescription($lang);

        return view('matter.summary', compact('description'));
    }

    public static function getOPSfamily($docnum)
    {
        $ops_key = env('OPS_APP_KEY');
        $ops_secret = env('OPS_SECRET');
        $token_url = 'https://ops.epo.org/3.2/auth/accesstoken';
        $token_response = Http::withHeaders(
            [
                'Authorization' => 'Basic ' . base64_encode($ops_key . ':' . $ops_secret)
            ]
        )->asForm()->post($token_url, ['grant_type' => 'client_credentials']);

        //$ops_legal = "http://ops.epo.org/3.2/rest-services/family/application/docdb/$docnum/legal.json";
        // Using application number
        //$ops_biblio = "https://ops.epo.org/3.2/rest-services/family/application/docdb/$docnum/biblio.json";
        // Using publication number
        $ops_biblio = "https://ops.epo.org/3.2/rest-services/family/publication/docdb/$docnum/biblio.json";
        $ops_response = Http::withToken($token_response['access_token'])
            ->asForm()
            ->get($ops_biblio);

        if ($ops_response->clientError()) {
            return ['errors' => ['docnum' => ['Number not found']], 'message' => 'Number not found in OPS Family'];
        }
        if ($ops_response->serverError()) {
            return ['exception' => 'OPS server error', 'message' => 'OPS server error, try again'];
        }

        $members = data_get($ops_response, 'ops:world-patent-data.ops:patent-family.ops:family-member');
        if (Arr::isList($members)) {
            // Sort members by increasing filing date and doc-id, so that the first is the priority application
            $members = collect($members)->sortBy(fn($member) => $member['application-reference']['document-id']['date']['$'] . $member['application-reference']['@doc-id']);
            // Group all members by doc-id, so that publications and grants appear in a same record (yet as two arrays)
            $members = collect($members)->groupBy(fn($member) => $member['application-reference']['@doc-id']);
        } else {
            // Turn single element into a list of one element
            $members = [$members['application-reference']['@doc-id'] => [0 => $members]];
        }
        $apps = [];
        $i = 0;
        foreach ($members as $key => $member) {
            // [0] is the item referring to the publication and [1] is the item referring to the grant
            $app = $member[0]['application-reference']['document-id'];
            // Don't want filings of EP translations
            if ($app['kind']['$'] == 'T') {
                continue;
            }
            // $key is the @doc-id
            $apps[$i]['id'] = $key;

            if (Arr::isList($member[0]['priority-claim'])) {
                $pri = collect($member[0]['priority-claim'])->where('priority-active-indicator.$', 'YES')->toArray();
            } else {
                // Turn single element into a list of one element
                $pri = [0 => $member[0]['priority-claim']];
            }

            foreach ($pri as $k => $p) {
                $apps[$i]['pri'][$k]['country'] = $p['document-id']['country']['$'];
                $apps[$i]['pri'][$k]['number'] = $p['document-id']['doc-number']['$'];
                $apps[$i]['pri'][$k]['kind'] = $p['document-id']['kind']['$'];
                $apps[$i]['pri'][$k]['date'] = date('Y-m-d', strtotime($p['document-id']['date']['$']));
            }

            $apps[$i]['app']['date'] = date('Y-m-d', strtotime($app['date']['$']));
            $apps[$i]['app']['kind'] = $app['kind']['$'];
            if ($app['kind']['$'] == 'W') {
                $country = 'WO';
                $app_number = $app['country']['$'] . $app['doc-number']['$'];
            } else {
                $country = $app['country']['$'];
                $app_number = $app['doc-number']['$'];
            }
            if ($country == 'US') {
                if (strlen($app_number) == 8) {
                    // Get only the first six digits, removing YY from the end. The serial is below 13 and is missing from the number
                    $app_number = substr($app_number, 0, 6);
                } else {
                    // Remove the YYYY prefix
                    $app_number = substr($app_number, 4);
                }
            }
            $apps[$i]['app']['country'] = $country;
            $apps[$i]['app']['number'] = $app_number;

            // Data taken from EP or PCT case
            if ((in_array($apps[$i]['app']['country'], ['EP', 'WO'])) && !data_get($apps, '0.pri.title')) {
                // Title (the last is the English title)
                $apps[0]['title'] = collect($member[0]['exchange-document']['bibliographic-data']['invention-title'])->last()['$'];

                // Each inventor is under [i]['inventor-name']['name']['$'] both in "epodoc" and "original" format indicated by [i]['@data-format']
                // take the higher half of the array indexes in original format
                $inventors = collect($member[0]['exchange-document']['bibliographic-data']['parties']['inventors']['inventor'])
                    ->where('@data-format', 'original');
                $apps[0]['inventors'] = $inventors->values()->pluck('inventor-name.name.$');

                // Each applicant is under [i]['applicant-name']['name']['$'] both in "epodoc" and "original" format indicated by [i]['@data-format']
                // take the higher half of the array indexes in original format
                $applicants = collect($member[0]['exchange-document']['bibliographic-data']['parties']['applicants']['applicant'])
                    ->where('@data-format', 'original');
                $apps[0]['applicants'] = $applicants->values()->pluck('applicant-name.name.$');

                // Get procedural steps
                $ops_procedure = "https://ops.epo.org/3.2/rest-services/register/application/epodoc/EP$app_number/procedural-steps";
                $ops_response = Http::withToken($token_response['access_token'])
                    ->asForm()
                    ->get($ops_procedure);

                if ($ops_response->successful()) {
                    $xml = new SimpleXMLElement($ops_response);
                    $steps = $xml->xpath('//reg:procedural-step');
                    $proc = [];
                    foreach ($steps as $k => $step) {
                        $proc[$k]['code'] = (string)$step->xpath('reg:procedural-step-code')[0];
                        if ($date = $step->xpath('reg:procedural-step-date[@step-date-type="DATE_OF_REQUEST"]/reg:date')) {
                            $proc[$k]['request'] = date('Y-m-d', strtotime($date[0]));
                        }
                        if ($date = $step->xpath('reg:procedural-step-date[@step-date-type="DATE_OF_DISPATCH"]/reg:date')) {
                            $proc[$k]['dispatched'] = date('Y-m-d', strtotime($date[0]));
                        }
                        if ($date = $step->xpath('reg:procedural-step-date[@step-date-type="DATE_OF_REPLY"]/reg:date')) {
                            $proc[$k]['replied'] = date('Y-m-d', strtotime($date[0]));
                        }
                        if ($date = $step->xpath('reg:procedural-step-date[@step-date-type="DATE_OF_PAYMENT"]/reg:date')) {
                            $proc[$k]['ren_paid'] = date('Y-m-d', strtotime($date[0]));
                        }
                        if ($date = $step->xpath('reg:procedural-step-date[@step-date-type="GRANT_FEE_PAID"]/reg:date')) {
                            $proc[$k]['grt_paid'] = date('Y-m-d', strtotime($date[0]));
                        }
                        if ($year = $step->xpath('reg:procedural-step-text[@step-text-type="YEAR"]')) {
                            $proc[$k]['ren_year'] = (int)$year[0];
                        }
                    }
                    $apps[$i]['procedure'] = $proc;
                }
            }

            if (in_array($apps[$i]['app']['country'], ['FR', 'US'])) {
                // Get legal
                $ops_procedure = "https://ops.epo.org/3.2/rest-services/legal/application/docdb/{$apps[$i]['app']['country']}$app_number";
                $ops_response = Http::withToken($token_response['access_token'])
                    ->asForm()
                    ->get($ops_procedure);

                // if ($ops_response->clientError()) {
                //     return ['errors' => ['docnum' => ['Number not found']], 'message' => 'Number not found in OPS Legal'];
                // }
                // if ($ops_response->serverError()) {
                //     return ['exception' => 'OPS server error', 'message' => 'OPS server error, try again'];
                // }

                if ($ops_response->successful()) {
                    $xml = new SimpleXMLElement($ops_response);
                    // Get renewals. Code RFEE for FR and MAFP for US
                    $steps = $xml->xpath('//ops:legal[@code="PLFP"] | //ops:legal[@code="MAFP"]');
                    $proc = [];
                    foreach ($steps as $k => $step) {
                        // Code compatible with EP procedural steps
                        $proc[$k]['code'] = 'RFEE';
                        if ($date = $step->xpath('ops:L007EP')) {
                            $proc[$k]['ren_paid'] = date('Y-m-d', strtotime($date[0]));
                        }
                        if ($year = $step->xpath('ops:L500EP/ops:L520EP')) {
                            $proc[$k]['ren_year'] = (int)$year[0];
                        }
                    }
                    $apps[$i]['procedure'] = $proc;
                }
            }
            // The publication and the grant have been grouped into a single member as two publication references to iterate through
            foreach ($member as $event) {
                // Take DOCDB format
                $pub = collect($event['publication-reference']['document-id'])->where('@document-id-type', 'docdb')->first();
                //return $pub;
                switch ($pub['kind']['$']) {
                    case 'A':
                    case 'A1':
                    case 'A2':
                        $apps[$i]['pub']['country'] = $pub['country']['$'];
                        $apps[$i]['pub']['number'] = $pub['doc-number']['$'];
                        $apps[$i]['pub']['date'] = date('Y-m-d', strtotime($pub['date']['$']));
                        break;
                    case 'B':
                    case 'B1':
                    case 'B2':
                        $apps[$i]['grt']['country'] = $pub['country']['$'];
                        $apps[$i]['grt']['number'] = $pub['doc-number']['$'];
                        $apps[$i]['grt']['date'] = date('Y-m-d', strtotime($pub['date']['$']));
                        // Find EP validations (doesn't always work)
                        // if ($pub['country']['$'] == 'EP') {
                        //     $ops = Http::withToken($token_response['access_token'])
                        //         ->asForm()->get("https://ops.epo.org/3.2/rest-services/legal/publication/docdb/EP{$pub['doc-number']['$']}.json")
                        //         ->json()['ops:world-patent-data']['ops:patent-family']['ops:family-member'];

                        //     // Create list of validation countries identified by the EPO and remove null array elements
                        //     $ep_val = @collect($ops[0]['ops:legal'])->pluck('ops:L500EP.ops:L501EP.$')->reject(function ($item, $key) {
                        //         return $item == null;
                        //     });
                        //     $apps[$i]['grt']['validations'] = $ep_val;
                        // }
                        break;
                }

                // PCT origin
                if ($pct_nat = collect($event['priority-claim'])->where('priority-linkage-type.$', 'W')->first()) {
                    $apps[$i]['pct'] = $pct_nat['document-id']['country']['$'] . $pct_nat['document-id']['doc-number']['$'];
                } else {
                    $apps[$i]['pct'] = null;
                }

                // Possible divisional
                if ($div = collect($event['priority-claim'])->where('priority-linkage-type.$', '3')->first()) {
                    $app_number = $div['document-id']['doc-number']['$'];
                    if ($div['document-id']['country']['$'] == 'US') {
                        if (strlen($app_number) == 8) {
                            // Get only the first six digits, removing YY from the end. The serial is below 13 and is missing from the number
                            $app_number = substr($app_number, 0, 6);
                        } else {
                            // Remove the YYYY prefix
                            $app_number = substr($app_number, 4);
                        }
                    }
                    $apps[$i]['div'] = $app_number;
                } else {
                    $apps[$i]['div'] = null;
                }

                // Possible continuation
                if ($div = collect($event['priority-claim'])->whereIn('priority-linkage-type.$', ['1', '2', 'C'])->first()) {
                    $app_number = $div['document-id']['doc-number']['$'];
                    if ($div['document-id']['country']['$'] == 'US') {
                        if (strlen($app_number) == 8) {
                            // Get only the first six digits, removing YY from the end. The serial is below 13 and is missing from the number
                            $app_number = substr($app_number, 0, 6);
                        } else {
                            // Remove the YYYY prefix
                            $app_number = substr($app_number, 4);
                        }
                    }
                    $apps[$i]['cnt'] = $app_number;
                } else {
                    $apps[$i]['cnt'] = null;
                }
            }
            $i++;
        }

        return $apps;
    }
}
