<?php

namespace App\Http\Controllers;

use App\Matter;
use App\Event;
use App\ActorPivot;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Http;

class MatterController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->except([
            'display_with',
            'page',
            'filter',
            'value',
            'sortkey',
            'sortdir',
            'tab',
            'include_dead'
        ]);
        
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
     * @param  int  $id
     * @return Json
    **/
    public function info($id)
    {
        return Matter::with(['tasksPending.info', 'renewalsPending', 'events.info', 'titles', 'actors', 'classifiers'])
            ->find($id);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $this->authorize('create', Matter::class);
        $operation = $request->input('operation', 'new'); // new, clone, child, ops
        $category = [];
        $category_code = $request->input('category', 'PAT');
        if ($operation != 'new' && $operation != 'ops') {
            $parent_matter = Matter::with('container', 'countryInfo', 'originInfo', 'category', 'type')
                ->find($request->matter_id);
            if ($operation == 'clone') {
                // Generate the next available caseref based on the prefix
                $parent_matter->caseref = Matter::where('caseref', 'like', $parent_matter->category->ref_prefix . '%')
                    ->max('caseref');
                ++$parent_matter->caseref;
            }
        } else {
            $parent_matter = new Matter; // Create empty matter object to avoid undefined errors in view
            $ref_prefix = \App\Category::find($category_code)['ref_prefix'];
            $category = [
                'code' => $category_code,
                'next_caseref' =>  Matter::where('caseref', 'like', $ref_prefix . '%')
                    ->max('caseref'),
                'name' => \App\Category::find($category_code)['category']
            ];
            ++$category['next_caseref'];
        }
        return view('matter.create', compact('parent_matter', 'operation', 'category'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->authorize('create', Matter::class);
        $this->validate($request, [
            'category_code' => 'required',
            'caseref' => 'required',
            'country' => 'required',
            'responsible' => 'required',
            'expire_date' => 'date'
        ]);

        // Unique UID handling
        $matters = Matter::where([
            ['caseref', $request->caseref],
            ['country', $request->country],
            ['category_code', $request->category_code],
            ['origin', $request->origin],
            ['type_code', $request->type_code]
        ]);

        $request->merge([ 'creator' => Auth::user()->login ]);

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
                    $new_matter->events()->create([
                        'code' => 'ENT',
                        'event_date' => now(),
                        'detail' => 'Child filing date'
                    ]);
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

    /**
     * Store multiple newly created resources in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeN(Request $request)
    {
        $this->authorize('create', Matter::class);
        $this->validate($request, [
            'ncountry' => 'required:array'
        ]);

        $parent_id = $request->parent_id;
        $parent_matter = Matter::with('priority', 'filing', 'publication', 'grant', 'classifiersNative')
            ->find($parent_id);

        foreach ($request->ncountry as $country) {
            $request->merge([
              'country' => $country,
              'creator' => Auth::user()->login
            ]);

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

            // Insert "entered" event
            $new_matter->events()->create(["code" => 'ENT', "event_date" => now()]);
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
        $this->authorize('create', Matter::class);
        $this->validate($request, [
            'app_num' => 'required',
            'caseref' => 'required',
            'category_code' => 'required',
            'client_id' => 'required'
        ]);

        $apps = collect($this->getOPSfamily($request->app_num));
        $ep_app = $apps->where('app.country' == 'EP')->keys()->first();
        $pct_app = $apps->where('pub.country' == 'WO')->keys()->first();
        $pct_id = 0;
        $first_app_id = 0;
        foreach ($apps as $key => &$app) {
            $request->merge([
              'country' => $app['app']['country'],
              'creator' => Auth::user()->login
            ]);
            if ($app['pct'] != null) {
                $request->merge(['origin' => 'WO']);
            }
            if ($app['div'] != null) {
                $request->merge(['type_code' => 'DIV']);
            }
            if ($app['cnt'] != null) {
                $request->merge(['type_code' => 'CNT']);
            }

            // Unique UID handling
            $matters = Matter::where([
                ['caseref', $request->caseref],
                ['country', $request->country],
                ['category_code', $request->category_code],
                ['origin', $request->origin],
                ['type_code', $request->type_code]
            ]);
            
            $idx = $matters->count();

            if ($idx > 0) {
                $request->merge(['idx' => $idx + 1]);
            }

            $new_matter = Matter::create($request->except(['_token', '_method', 'app_num', 'client_id']));
            $app['matter_id'] = $new_matter->id;
            if ($app['app']['country'] == 'WO') {
                $pct_id = $new_matter->id;
            }
            if ($key == 0) {
                $first_app_id = $new_matter->id;
                $new_matter->classifiersNative()->create(['type_code' => 'TIT', 'value' => $app['pri']['title']]);
                $new_matter->actorPivot()->create(['actor_id' => $request->client_id, 'role' => 'CLI', 'shared' => 1]);
            } else {
                $new_matter->container_id = $first_app_id;
                $new_matter->events()->create(["code" => 'PRI', 'alt_matter_id' => $first_app_id]);
            }
            if ($app['pct'] != null && $pct_id != 0) {
                $new_matter->parent_id = $pct_id;
                $new_matter->events()->create(['code' => 'PFIL', 'alt_matter_id' => $pct_id]);
            }
            $new_matter->events()->create(["code" => 'FIL', "event_date" => $app['app']['date'], 'detail' => $app['app']['number']]);
            $new_matter->events()->create(["code" => 'PUB', "event_date" => $app['pub']['date'], 'detail' => $app['pub']['number']]);
            if (array_key_exists('grt', $app)) {
                $new_matter->events()->create(["code" => 'GRT', "event_date" => $app['grt']['date'], 'detail' => $app['grt']['number']]);
            }

            $new_matter->save();
        }

        return response()->json(['redirect' => "/matter?Ref=$request->caseref"]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Matter  $matter
     * @return \Illuminate\Http\Response
     */
    public function edit(Matter $matter)
    {
        $this->authorize('update', $matter);
        $matter->load(
            'container',
            'parent',
            'countryInfo:iso,name',
            'originInfo:iso,name',
            'category',
            'type',
            'filing'
        );
        $country_edit = $matter->tasks()->whereHas('rule', function (Builder $q) {
            $q->whereNull('for_country');
        })->count();
        $cat_edit = $matter->tasks()->whereHas('rule', function (Builder $q) {
            $q->whereNull('for_category');
        })->count();
        return view("matter.edit", compact(['matter', 'cat_edit', 'country_edit']));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Matter  $matter
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Matter $matter)
    {
        $this->authorize('update', $matter);
        $request->validate([
            'term_adjust' => 'numeric',
            'idx' => 'numeric|nullable',
            'expire_date' => 'date'
        ]);
        $request->merge([ 'updater' => Auth::user()->login ]);
        $matter->update($request->except(['_token', '_method']));
        return $matter;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Matter  $matter
     * @return \Illuminate\Http\Response
     */
    public function destroy(Matter $matter)
    {
        $this->authorize('delete', $matter);
        $matter->delete();
        return $matter;
    }

    /**
     * Exports Matters list
     * *
     */
    public function export(Request $request)
    {
        $filters = $request->except([
            'display_with',
            'page',
            'filter',
            'value',
            'sortkey',
            'sortdir',
            'tab',
            'include_dead'
        ]);

        $export = Matter::filter(
            $request->input('sortkey', 'caseref'),
            $request->input('sortdir', 'asc'),
            $filters,
            $request->display_with,
            $request->include_dead
        )->get()->toArray();

        $captions = [
            'Our Ref',
            'Country',
            'Cat',
            'Origin',
            'Status',
            'Status date',
            'Client',
            'Client Ref',
            'Applicant',
            'Agent',
            'Agent Ref',
            'Title',
            'Title2',
            'Title3',
            'Inventor 1',
            'Filed',
            'FilNo',
            'Published',
            'Pub. No',
            'Granted',
            'Grt No',
            'ID',
            'container_ID',
            'parent_ID',
            'Type',
            'Responsible',
            'Delegate',
            'Dead',
            'Ctnr'
        ];

        $export_csv = fopen('php://memory', 'w');
        fputcsv($export_csv, $captions, ';');
        foreach ($export as $row) {
            fputcsv($export_csv, array_map("utf8_decode", $row), ';');
        }
        rewind($export_csv);
        $filename = Now()->isoFormat('YMMDDHHmmss') . '_matters.csv';

        return response()->stream(
            function () use ($export_csv) {
                fpassthru($export_csv);
            },
            200,
            [ 'Content-Type' => 'application/csv', 'Content-Disposition' => 'attachment; filename=' . $filename ]
        );
    }

    /**
     * Generate merged document on the fly from uploaded template
     * *
     */
    public function mergeFile(Matter $matter, Request $request)
    {
        // No dedicated "form request" class being defined, this validation will silently terminate the operation when unsuccessful
        $this->validate($request, [
            'file'  => 'required|file|mimes:docx,dotx'
        ]);
        $file = $request->file('file');

        // Attempt for a cleaner creation method of the data collection using relationships
        // $data = collect();
        // $data->id = $matter->id;
        // $data->File_Ref = $matter->ui;
        // $data->Country = $matter->country;
        // $data->File_Category = $matter->category_code;
        // $data->Filing_Date = $matter->filing->event_date->isoFormat('L');
        // $data->Filing_Number = $matter->filing->detail;
        // $data->Pub_Date = $matter->publication->event_date->isoFormat('L');
        // $data->Pub_Number = $matter->publication->detail;
        // $data->Priority = $matter->priority;
        // // "GROUP_CONCAT(DISTINCT CONCAT(pri.country, pri.detail, ' - ', DATE_FORMAT(pri.event_date, '%d/%m/%Y'))
        // //    SEPARATOR '\n') AS Priority"
        // $data->Grant_Date = $matter->grant->event_date->isoFormat('L');
        // $data->Grant_Number = $matter->grant->detail;
        // $data->Registration_Date = $matter->registration->event_date->isoFormat('L');
        // $data->Registration_Number = $matter->registration->detail;
        // $data->Pub_Reg_Date = $matter->events()->whereCode('PR')->first()->event_date->isoFormat('L');
        // $data->Pub_Reg_Number = $matter->events()->whereCode('PR')->first()->detail;
        // $data->Allowance_Date = $matter->events()->whereCode('ALL')->first()->event_date->isoFormat('L');
        // $data->Expiration_Date = $matter->expire_date;
        // $data->Client = $matter->client->name;
        // $data->Client_Address = $matter->client->address;
        // $data->Client_Country = $matter->client->country;
        // $data->Contact = $matter->contact->name;
        // if ($matter->client->address_billing) {
        //     $data->Billing_Address = $matter->client->address_billing . '\n' . $matter->client->country_billing;
        // } else {
        //     $data->Billing_Address = $matter->client->name . '\n' . $matter->client->address . '\n' . $matter->client->country;
        // }
        // $data->Client_Ref = $matter->client->actor_ref;
        // $data->Email = $matter->client->email;
        // $data->VAT = $matter->client->VAT_number;
        // $data->Title = $matter->titles()->whereTypeCode('TIT')->first()->value;
        // $data->Official_Title = $matter->titles()->whereTypeCode('TITOF')->first()->value ?? $data->Title;
        // $data->English_Title = $matter->titles()->whereTypeCode('TITEN')->first()->value ?? $data->Official_Title;
        // $data->Trademark = $matter->titles()->whereTypeCode('TM')->first()->value;
        // $data->Classes = $matter->classifiers()->whereTypeCode('TMCL')->get()->implode('value', '.');
        // $data->Inventors = $matter->actors()->whereRoleCode('INV')->orderBy('display_order')->get()->implode('name', ' - ');
        // "GROUP_CONCAT(DISTINCT CONCAT_WS(' ', inv.name, inv.first_name)
        //     ORDER BY linv.display_order ASC
        //     SEPARATOR ' - ') AS Inventors"),
        // "GROUP_CONCAT(DISTINCT CONCAT_WS('\n', CONCAT_WS(' ', inv.name, inv.first_name), inv.address, inv.country, inv.nationality)
        //     ORDER BY linv.display_order ASC
        //     SEPARATOR '\n\n') AS Inventor_Addresses"),
        // "IF(GROUP_CONCAT(DISTINCT ownc.name) IS NOT NULL OR GROUP_CONCAT(DISTINCT own.name) IS NOT NULL,
        //     CONCAT_WS('\n', GROUP_CONCAT(DISTINCT ownc.name SEPARATOR '\n'), GROUP_CONCAT(DISTINCT own.name SEPARATOR '\n')),
        //     CONCAT_WS('\n', GROUP_CONCAT(DISTINCT applc.name SEPARATOR '\n'), GROUP_CONCAT(DISTINCT appl.name SEPARATOR '\n'))
        // ) AS Owner"),
        // "CONCAT_WS('\n', agt.name, agt.address, agt.country) AS Agent"),
        // 'lagt.actor_ref AS Agent_Ref',
        // 'resp.name AS Responsible',
        // 'wri.name AS Writer',
        // 'ann.name AS Annuity_Agent'

        
        $data = Matter::select(
            'matter.id',
            'matter.uid AS File_Ref',
            'matter.country AS Country',
            'matter.category_code AS File_Category',
            DB::raw("DATE_FORMAT(fil.event_date, '%d/%m/%Y') AS Filing_Date"),
            'fil.detail AS Filing_Number',
            DB::raw("DATE_FORMAT(pub.event_date, '%d/%m/%Y') AS Pub_Date"),
            'pub.detail AS Pub_Number',
            DB::raw("GROUP_CONCAT(DISTINCT CONCAT(pri.country, pri.detail, ' - ', DATE_FORMAT(pri.event_date, '%d/%m/%Y'))
                SEPARATOR '\n') AS Priority"),
            DB::raw("DATE_FORMAT(grt.event_date, '%d/%m/%Y') AS Grant_Date"),
            'grt.detail AS Grant_Number',
            DB::raw("DATE_FORMAT(reg.event_date, '%d/%m/%Y') AS Registration_Date"),
            'reg.detail AS Registration_Number',
            DB::raw("DATE_FORMAT(pr.event_date, '%d/%m/%Y') AS Pub_Reg_Date"),
            'pr.detail AS Pub_Reg_Number',
            DB::raw("DATE_FORMAT(allow.event_date, '%d/%m/%Y') AS Allowance_Date"),
            'matter.expire_date AS Expiration_Date',
            DB::raw("COALESCE(cli.name, clic.name) AS Client"),
            DB::raw("COALESCE(cli.address, clic.address) AS Client_Address"),
            DB::raw("COALESCE(cli.country, clic.country) AS Client_Country"),
            'cnt.name AS Contact',
            DB::raw("IF(COALESCE(cli.address_billing, clic.address_billing) IS NULL,
                CONCAT_WS('\n', COALESCE(pay.name, payc.name, cli.name, clic.name), COALESCE(pay.address, payc.address, cli.address, clic.address), COALESCE(pay.country, payc.country, cli.country, clic.country)),
                CONCAT_WS('\n', COALESCE(pay.name, payc.name), COALESCE(pay.address, payc.address, cli.address_billing, clic.address_billing), COALESCE(pay.country, payc.country, cli.country_billing, clic.country_billing))
            ) AS Billing_Address"),
            DB::raw("COALESCE(lcli.actor_ref, lclic.actor_ref) AS Client_Ref"),
            DB::raw("COALESCE(cli.email, clic.email) AS Email"),
            DB::raw("COALESCE(cli.VAT_number, clic.VAT_number) AS VAT"),
            DB::raw("COALESCE(titof.value, tit.value) AS Official_Title"),
            DB::raw("COALESCE(titen.value, titof.value, tit.value) AS English_Title"),
            'tit.value AS Title',
            'tm.value AS Trademark',
            DB::raw("GROUP_CONCAT(DISTINCT class.value SEPARATOR '.') AS Classes"),
            DB::raw("GROUP_CONCAT(DISTINCT CONCAT_WS(' ', inv.name, inv.first_name)
                ORDER BY linv.display_order ASC
                SEPARATOR ' - ') AS Inventors"),
            DB::raw("GROUP_CONCAT(DISTINCT CONCAT_WS('\n', CONCAT_WS(' ', inv.name, inv.first_name), inv.address, inv.country, inv.nationality)
                ORDER BY linv.display_order ASC
                SEPARATOR '\n\n') AS Inventor_Addresses"),
            DB::raw("IF(GROUP_CONCAT(DISTINCT ownc.name) IS NOT NULL OR GROUP_CONCAT(DISTINCT own.name) IS NOT NULL,
                CONCAT_WS('\n', GROUP_CONCAT(DISTINCT ownc.name SEPARATOR '\n'), GROUP_CONCAT(DISTINCT own.name SEPARATOR '\n')),
                CONCAT_WS('\n', GROUP_CONCAT(DISTINCT applc.name SEPARATOR '\n'), GROUP_CONCAT(DISTINCT appl.name SEPARATOR '\n'))
            ) AS Owner"),
            DB::raw("CONCAT_WS('\n', agt.name, agt.address, agt.country) AS Agent"),
            'lagt.actor_ref AS Agent_Ref',
            'resp.name AS Responsible',
            'wri.name AS Writer',
            'ann.name AS Annuity_Agent'
        )
        ->leftJoin(
            DB::raw("matter_actor_lnk linv
            JOIN actor inv ON inv.id = linv.actor_id AND linv.role = 'INV'"),
            DB::raw('IFNULL(matter.container_id, matter.id)'),
            'linv.matter_id'
        )
        ->leftJoin(
            DB::raw("matter_actor_lnk lcli
            JOIN actor cli ON cli.id = lcli.actor_id
            AND lcli.role = 'CLI' AND lcli.display_order = 1"),
            'matter.id',
            'lcli.matter_id'
        )
        ->leftJoin(
            DB::raw("matter_actor_lnk lclic
            JOIN actor clic ON clic.id = lclic.actor_id
            AND lclic.role = 'CLI'
            AND lclic.display_order = 1
            AND lclic.shared = 1"),
            'matter.container_id',
            'lclic.matter_id'
        )
        ->leftJoin(
            DB::raw("matter_actor_lnk lpay
            JOIN actor pay ON pay.id = lpay.actor_id
            AND lpay.role = 'PAY' AND lpay.display_order = 1"),
            'matter.id',
            'lpay.matter_id'
        )
        ->leftJoin(
            DB::raw("matter_actor_lnk lpayc
            JOIN actor payc ON payc.id = lpayc.actor_id
            AND lpayc.role = 'PAY'
            AND lpayc.display_order = 1
            AND lpayc.shared = 1"),
            'matter.container_id',
            'lpayc.matter_id'
        )
        ->leftJoin(
            DB::raw("matter_actor_lnk lappl
            JOIN actor appl ON appl.id = lappl.actor_id
            AND lappl.role = 'APP'"),
            'matter.id',
            'lappl.matter_id'
        )
        ->leftJoin(
            DB::raw("matter_actor_lnk lapplc
            JOIN actor applc ON applc.id = lapplc.actor_id
            AND lapplc.role = 'APP'
            AND lapplc.shared = 1"),
            'matter.container_id',
            'lapplc.matter_id'
        )
        ->leftJoin(
            DB::raw("matter_actor_lnk lown
            JOIN actor own ON own.id = lown.actor_id
            AND lown.role = 'OWN'"),
            'matter.id',
            'lown.matter_id'
        )
        ->leftJoin(
            DB::raw("matter_actor_lnk lownc
            JOIN actor ownc ON ownc.id = lownc.actor_id
            AND lownc.role = 'OWN'
            AND lownc.shared = 1"),
            'matter.container_id',
            'lownc.matter_id'
        )
        ->leftJoin(
            DB::raw("matter_actor_lnk lann
            JOIN actor ann ON ann.id = lann.actor_id
            AND lann.role = 'ANN'"),
            'matter.id',
            'lann.matter_id'
        )
        ->leftJoin(
            DB::raw("matter_actor_lnk lcnt
            JOIN actor cnt ON cnt.id = lcnt.actor_id
            AND lcnt.role = 'CNT'"),
            DB::raw('IFNULL(matter.container_id, matter.id)'),
            'lcnt.matter_id'
        )
        ->leftJoin(
            DB::raw("matter_actor_lnk lagt
            JOIN actor agt ON agt.id = lagt.actor_id
            AND lagt.role = 'AGT'"),
            'matter.id',
            'lagt.matter_id'
        )
        ->leftJoin(
            DB::raw("matter_actor_lnk lwri
            JOIN actor wri ON wri.id = lwri.actor_id
            AND lwri.role = 'WRI'"),
            'matter.id',
            'lwri.matter_id'
        )
        ->leftJoin('event AS fil', function ($join) {
            $join->on('matter.id', 'fil.matter_id')->where('fil.code', 'FIL');
        })
        ->leftJoin('event AS pub', function ($join) {
            $join->on('matter.id', 'pub.matter_id')->where('pub.code', 'PUB');
        })
        ->leftJoin('event AS grt', function ($join) {
            $join->on('matter.id', 'grt.matter_id')->where('grt.code', 'GRT');
        })
        ->leftJoin('event AS reg', function ($join) {
            $join->on('matter.id', 'reg.matter_id')->where('reg.code', 'REG');
        })
        ->leftJoin('event AS pr', function ($join) {
            $join->on('matter.id', 'pr.matter_id')->where('pr.code', 'PR');
        })
        ->leftJoin('event_lnk_list AS pri', function ($join) {
            $join->on('matter.id', 'pri.matter_id')->where('pri.code', 'PRI');
        })
        ->leftJoin('event AS allow', function ($join) {
            $join->on('matter.id', 'allow.matter_id')->where('allow.code', 'ALL');
        })
        ->leftJoin('classifier AS titof', function ($join) {
            $join->on('titof.matter_id', DB::raw('IFNULL(matter.container_id, matter.id)'))
            ->where('titof.type_code', 'TITOF');
        })
        ->leftJoin('classifier AS titen', function ($join) {
            $join->on('titen.matter_id', DB::raw('IFNULL(matter.container_id, matter.id)'))
            ->where('titen.type_code', 'TITEN');
        })
        ->leftJoin('classifier AS tit', function ($join) {
            $join->on('tit.matter_id', DB::raw('IFNULL(matter.container_id, matter.id)'))
            ->where('tit.type_code', 'TIT');
        })
        ->leftJoin('classifier AS tm', function ($join) {
            $join->on('tm.matter_id', DB::raw('IFNULL(matter.container_id, matter.id)'))
            ->where('tm.type_code', 'TM');
        })
        ->leftJoin('classifier AS class', function ($join) {
            $join->on('class.matter_id', DB::raw('IFNULL(matter.container_id, matter.id)'))
            ->where('class.type_code', 'TMCL');
        })
        ->join('actor AS resp', 'resp.login', 'matter.responsible')
        ->find($matter->id);

        // Exclude the data having line breaks
        $simpledata = collect($data)->except([
            'Priority',
            'Client_Address',
            'Billing_Address',
            'Inventor_Addresses',
            'Owner',
            'Agent'
        ])->toArray();
        // Data having line breaks
        $complexdata = collect($data)->only([
            'Priority',
            'Client_Address',
            'Billing_Address',
            'Inventor_Addresses',
            'Owner',
            'Agent'
        ]);

        $template = new \PhpOffice\PhpWord\TemplateProcessor($file->path());
        \PhpOffice\PhpWord\Settings::setOutputEscapingEnabled(true);
        $template->setValues($simpledata);
        // Process the data having line breaks and replace the line breaks with ${nl} macros
        foreach ($complexdata as $key => $item) {
            $item = str_replace("\n", "\${nl}", $item);
            $template->setValue($key, $item);

            /*
             * Cleaner method for processing the line breaks, but not fully operational (the style of the placeholder is not applied but replaced by "Normal")
             */
            // $textrun = new \PhpOffice\PhpWord\Element\TextRun();
            // $textlines = explode("\n", $item);
            // $textrun->addText(array_shift($textlines));
            // foreach ($textlines as $line) {
            //     $textrun->addTextBreak();
            //     $textrun->addText($line);
            // }
            // $template->setComplexValue($key, $textrun);
            // unset($textlines);
        }

        // Prevent escaping the line break tags
        \PhpOffice\PhpWord\Settings::setOutputEscapingEnabled(false);
        // Set the ${nl} macros to line break tags (replacing "\n" directly with "<w:br/>" causes escaping issues)
        $template->setValue('nl', '<w:br/>');
        
        header("Content-Description: File Transfer");
        header('Content-Disposition: attachment; filename="merged-' . $file->getClientOriginalName()) . '"';
        header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
        header('Content-Transfer-Encoding: binary');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Expires: 0');
        $template->saveAs('php://output');
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
        $events = Event::with(['tasks' => function ($query) {
            $query->where('code', 'REN');
        }])->whereHas('tasks', function ($query) {
            $query->where('code', 'REN');
        })->where('matter_id', $matter->id)->get();
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
        $description = $matter->getDescription($matter->id, $lang);
        return view('matter.summary', compact('description'));
    }

    public static function getOPSfamily($app_num)
    {
        $ops_key = env('OPS_APP_KEY');
		$ops_secret = env('OPS_SECRET');
        $token_url = 'https://ops.epo.org/3.2/auth/accesstoken';
        $token_response = Http::withHeaders([
            'Authorization' => 'Basic ' .base64_encode($ops_key.':'.$ops_secret)
        ])->asForm()->post($token_url, ['grant_type' => 'client_credentials']);
        
        //$ops_legal = "http://ops.epo.org/3.2/rest-services/family/application/docdb/$app_num/legal.json";
        $ops_biblio = "https://ops.epo.org/3.2/rest-services/family/application/docdb/$app_num/biblio.json";
        $ops_response = Http::withToken($token_response['access_token'])
            ->asForm()
            ->get($ops_biblio);

        //$ops = $ops_response->collect();

        if ($ops_response->clientError()) {
            return response()->json(['errors' => ['app_num' => ['Number not found']], 'message' => 'Number not found in OPS']);
        }

        if ($ops_response->serverError()) {
            return response()->json(['errors' => ['app_num' => ['OPS server error']], 'message' => 'OPS server error, try again']);
        }
       
        $members = collect($ops_response['ops:world-patent-data']['ops:patent-family']['ops:family-member']);
        // Sort members by increasing doc-id, i.e. by increasing filing date so that the first is the priority application
        $sorted = $members->sortBy(function ($member, $key) {
            return $member['application-reference']['@doc-id'];
        });
        // Group all members by doc-id, so that publications and grants appear in a same record (yet as two arrays)
        $grouped = $sorted->groupBy(function ($member, $key) {
            return $member['application-reference']['@doc-id'];
        });
        
        $apps = [];
        $app = $grouped->first()->first()['priority-claim']['document-id'];
        $apps[0]['pri']['date'] = date("Y-m-d", strtotime($app['date']['$']));
        $apps[0]['pri']['country'] = $app['country']['$'];
        $apps[0]['pri']['number'] = $app['doc-number']['$'];
        
        // Title
        $apps[0]['pri']['title'] = collect($grouped->first()->first()['exchange-document']['bibliographic-data']['invention-title'])->last()['$'];

        // Each inventor is under [i]['inventor-name']['name']['$'] both in "epodoc" and "original" format indicated by [i]['@data-format']
        // take the higher half of the array indexes in original format
         $inventors = collect($grouped->first()->first()['exchange-document']['bibliographic-data']['parties']['inventors']['inventor'])
            ->where('@data-format', 'original');
         $apps[0]['pri']['inventors'] = $inventors->values()->pluck('inventor-name.name.$');

        // Each applicant is under [i]['applicant-name']['name']['$'] both in "epodoc" and "original" format indicated by [i]['@data-format']
        // take the higher half of the array indexes in original format
        $applicants = collect($grouped->first()->first()['exchange-document']['bibliographic-data']['parties']['applicants']['applicant'])
            ->where('@data-format', 'original');
        $apps[0]['pri']['applicants'] = $applicants->values()->pluck('applicant-name.name.$');
        
        $i = 0;
        foreach ($grouped as $item) {
            // The first is in DOCDB format, the other (not used) in EPODOC format
            $app = $item->first()['application-reference']['document-id'];
            $apps[$i]['app']['date'] = date("Y-m-d", strtotime($app['date']['$']));

            if ($app['kind']['$'] == 'W') {
                $apps[$i]['app']['country'] = 'WO';
                $apps[$i]['app']['number'] = $app['country']['$'] . $app['doc-number']['$'];
            } else {
                $apps[$i]['app']['country'] = $app['country']['$'];
                $apps[$i]['app']['number'] = $app['doc-number']['$'];
            }

            foreach ($item as $subitem) {
                $pub = $subitem['publication-reference']['document-id'][0];
                switch ($pub['kind']['$']) {
                    case 'A':
                    case 'A1':
                    case 'A2':
                        $apps[$i]['pub']['country'] = $pub['country']['$'];
                        $apps[$i]['pub']['number'] = $pub['doc-number']['$'];
                        $apps[$i]['pub']['date'] = date("Y-m-d", strtotime($pub['date']['$']));
                        break;
                    case 'B':
                    case 'B1':
                        $apps[$i]['grt']['country'] = $pub['country']['$'];
                        $apps[$i]['grt']['number'] = $pub['doc-number']['$'];
                        $apps[$i]['grt']['date'] = date("Y-m-d", strtotime($pub['date']['$']));
                        break;
                }
                // PCT origin
                $pct_nat = collect($subitem['priority-claim'])->where('priority-linkage-type.$', 'W')->first();
                $apps[$i]['pct'] = @$pct_nat['document-id']['doc-number']['$'];
                // Possible divisional
                $div = collect($subitem['priority-claim'])->where('priority-linkage-type.$', '3')->first();
                $apps[$i]['div'] = @$div['document-id']['doc-number']['$'];
                // Possible continuation
                $div = collect($subitem['priority-claim'])->where('priority-linkage-type.$', '1')->first();
                $apps[$i]['cnt'] = @$div['document-id']['doc-number']['$'];
            }
            $i++;
        }

        // Find EP validations (doesn't always work)
        // $ep_app = collect($apps)->where('app.country', 'EP')->first()['app'];
        // $ep_val = [];
        // if ($ep_app) {
        //     $ops = Http::withToken($token_response['access_token'])
        //         ->asForm()->get("https://ops.epo.org/3.2/rest-services/legal/application/docdb/$ep_app[country].$ep_app[number].json")
        //         ->json()['ops:world-patent-data']['ops:patent-family']['ops:family-member'];
            
        //     // Create list of validation countries identified by the EPO and remove null array elements
        //     $ep_val = @collect($ops[0]['ops:legal'])->pluck('ops:L500EP.ops:L501EP.$')->reject(function ($item, $key) {
        //         return $item == null;
        //     });
        // }

        //return $grouped->values()->all();
        //return $ep_val;
        return $apps;
    }
}
