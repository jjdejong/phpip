<?php

namespace App\Http\Controllers;

use App\Matter;
use App\Event;
use App\ActorPivot;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;

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
        $operation = $request->input('operation', 'new'); // new, clone, child
        $category = [];
        $category_code = $request->input('category');
        if ($operation != 'new') {
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
            if ($category_code != '') {
                $ref_prefix = \App\Category::find($category_code)['ref_prefix'];
                $category = [
                    'code' => $category_code,
                    'next_caseref' =>  Matter::where('caseref', 'like', $ref_prefix . '%')
                        ->max('caseref'),
                    'name' => \App\Category::find($category_code)['category']
                ];
                ++$category['next_caseref'];
            }
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

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Matter  $matter
     * @return \Illuminate\Http\Response
     */
    public function edit(Matter $matter)
    {
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
                CONCAT_WS('\n', COALESCE(cli.name, clic.name), COALESCE(cli.address, clic.address), COALESCE(cli.country, clic.country)),
                CONCAT_WS('\n', COALESCE(cli.address_billing, clic.address_billing), COALESCE(cli.country_billing, clic.country_billing))
            ) AS Billing_Adress"),
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
            'Billing_Adress',
            'Inventor_Addresses',
            'Owner',
            'Agent'
        ])->toArray();
        // Data having line breaks
        $complexdata = collect($data)->only([
            'Priority',
            'Client_Address',
            'Billing_Adress',
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
             * Alternative methods for processing the line breaks in a cleaner manner, but not fully operational
             */
            
            /*
            $textrun = new \PhpOffice\PhpWord\Element\TextRun();
            $textlines = explode("\n", $item);
            $textrun->addText(array_shift($textlines));
            foreach ($textlines as $line) {
                $textrun->addTextBreak();
                $textrun->addText($line);
            }
            $template->setComplexValue($key, $textrun);
            unset($textlines);
            */

            /* This method needs the macros to be placed in table cells
            $template->cloneRow($key, sizeof($textlines));
            $i = 1;
            foreach ($textlines as $line) {
                $template->setValue("$key#$i", $line);
                $i++;
            }
            unset($textlines);
            */

            /* Does not work
            // Extract (explode) each line and make it an element of an array of arrays (chunk)
            $textlines = array_chunk(explode("\n", $item), 1);
            foreach ($textlines as $line) {
                $line = array_fill_keys(['line'], $line[0]);
            }
            // Add the 'line' key to each line
            $textlines = array_map(function ($item) {
                return array_fill_keys(['line'], $item[0]);
            }, $textlines);
            $template->cloneBlock($key, 1, true, false, $textlines);
            */
        }

        // Prevent escaping the line break tags
        \PhpOffice\PhpWord\Settings::setOutputEscapingEnabled(false);
        // Set the ${nl} macros to line break tags
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

    /**
    * List Matters for actor by display name, where actors found are registered directly
    * inside or in the related container or parent
    * (not used?)
     * @param  string  $dname  value to search in display_name
     * @return Json     list of matters
    */
    // public function filesByActor(string $dname) {
    //     $dname = urldecode($dname);
    //     $actorpivot1 = new ActorPivot();
    //     $actorpivot2 = new ActorPivot();
    //     $actor_model = new Actor();
    //     $matter = new Matter();

    //     $actor_list = $actor_model->select(['id'])->where('display_name', 'like', $dname . '%')
    //         ->get()->toArray();
    //     $ma1 = $actorpivot1->select(['matter_id'])->whereIn('actor_id', $actor_list)
    //         ->get()->toArray();
    //     $ma2 = $actorpivot2->select(['matter_id'])->whereIn('actor_id', $actor_list)->where('shared', 1)
    //         ->get()->toArray();
    //     $matter_list1 = $matter->select('id')->whereIn('parent_id', $ma2)
    //         ->get()->toArray();
    //     $matter_list2 = $matter->select('id')->whereIn('container_id', $ma2)
    //         ->get()->toArray();
        
    //     $matter_actor = $matter->select(['id','caseref','suffix'])
    //         ->whereIn('id', array_merge($ma1, $matter_list1, $matter_list2))
    //         ->get();
    //     return $matter_actor;
    // }
}
