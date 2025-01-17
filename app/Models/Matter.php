<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Matter extends Model
{
    protected $table = 'matter';

    protected $hidden = ['creator', 'created_at', 'updated_at', 'updater'];

    protected $guarded = ['id', 'created_at', 'updated_at'];
    
    /*protected $casts = [
        'expire_date' => 'date:Y-m-d'
    ];*/

    public function family()
    {
        // Gets family members
        return $this->hasMany(Matter::class, 'caseref', 'caseref')
            ->orderBy('origin')
            ->orderBy('country')
            ->orderBy('type_code')
            ->orderBy('idx');
    }

    public function container()
    {
        return $this->belongsTo(Matter::class, 'container_id')->withDefault();
    }

    public function parent()
    {
        return $this->belongsTo(Matter::class, 'parent_id')->withDefault();
    }

    public function children()
    {
        return $this->hasMany(Matter::class, 'parent_id')
            ->orderBy('origin')
            ->orderBy('country')
            ->orderBy('type_code')
            ->orderBy('idx');
    }

    public function priorityTo()
    {
        // Gets external matters claiming priority on this one (where clause is ignored by eager loading)
        return $this->belongsToMany(Matter::class, 'event', 'alt_matter_id')
            ->where('caseref', '!=', $this->caseref)
            ->orderBy('caseref')
            ->orderBy('origin')
            ->orderBy('country')
            ->orderBy('type_code')
            ->orderBy('idx');
    }

    public function actors()
    {
        // MatterActors refers to a view that also includes the actors inherited from the container. Can only be used to display data
        return $this->hasMany(MatterActors::class);
    }

    public function client()
    {
        // Used in Policies - do not change without checking MatterPolicy
        return $this->hasOne(MatterActors::class)->whereRoleCode('CLI')->withDefault();
    }

    public function sharedClient()
    {
        return $this->hasOne(MatterActors::class)->whereRoleCode('CLI')->whereShared(1)->withDefault();
    }

    public function payer()
    {
        return $this->hasOne(MatterActors::class)->whereRoleCode('PAY')->withDefault();
    }

    public function sharedPayer()
    {
        return $this->hasOne(MatterActors::class)->whereRoleCode('PAY')->whereShared(1)->withDefault();
    }

    public function delegate()
    {
        return $this->actors()->whereRoleCode('DEL');
    }

    public function contact()
    {
        return $this->actors()->whereRoleCode('CNT');
    }

    public function applicants()
    {
        return $this->actors()->whereRoleCode('APP');
    }

    public function applicantsFromLnk()
    {
        return $this->belongsToMany(Actor::class, 'matter_actor_lnk', 'matter_id', 'actor_id')
            ->using(ActorPivot::class)
            ->withPivot('role', 'display_order', 'shared', 'actor_ref')
            ->wherePivot('role', 'APP');
    }

    public function sharedApplicantsFromLnk()
    {
        return $this->belongsToMany(Actor::class, 'matter_actor_lnk', 'matter_id', 'actor_id', 'container_id', '')
            ->using(ActorPivot::class)
            ->withPivot('role', 'display_order', 'shared', 'actor_ref')
            ->wherePivot('role', 'APP')
            ->wherePivot('shared', 1);
    }

    public function owners()
    {
        return $this->belongsToMany(Actor::class, 'matter_actor_lnk', 'matter_id', 'actor_id')
            ->using(ActorPivot::class)
            ->withPivot('role', 'display_order', 'shared', 'actor_ref')
            ->wherePivot('role', 'OWN');
    }

    public function sharedOwners()
    {
        return $this->belongsToMany(Actor::class, 'matter_actor_lnk', 'matter_id', 'actor_id', 'container_id', '')
            ->using(ActorPivot::class)
            ->withPivot('role', 'display_order', 'shared', 'actor_ref')
            ->wherePivot('role', 'OWN')
            ->wherePivot('shared', 1);
    }

    public function inventors()
    {
        return $this->hasMany(MatterActors::class)
            ->whereRoleCode('INV');
    }

    public function agents()
    {
        return $this->belongsToMany(Actor::class, 'matter_actor_lnk', 'matter_id', 'actor_id')
            ->using(ActorPivot::class)
            ->withPivot('role', 'display_order', 'shared', 'actor_ref')
            ->wherePivot('role', 'AGT');
    }

    public function writers()
    {
        return $this->hasMany(MatterActors::class)
            ->whereRoleCode('WRT');
    }

    public function annuityAgents()
    {
        return $this->belongsToMany(Actor::class, 'matter_actor_lnk', 'matter_id', 'actor_id')
            ->using(ActorPivot::class)
            ->withPivot('role', 'display_order', 'shared', 'actor_ref')
            ->wherePivot('role', 'ANN');
    }

    public function responsibles(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Actor::class, 'login', 'responsible');
    }

    public function actorPivot()
    {
        return $this->hasMany(ActorPivot::class);
    }

    public function events()
    {
        return $this->hasMany(Event::class)
            ->orderBy('event_date');
    }

    public function eventsFromView()
    {
        return $this->hasMany(EventLnkList::class, 'matter_id', 'id');
    }

    public function filing()
    {
        return $this->hasOne(Event::class)
            ->whereCode('FIL')->withDefault();
    }

    public function parentFiling()
    {
        return $this->hasMany(Event::class)
            ->whereCode('PFIL')->withDefault();
    }

    public function publication()
    {
        return $this->hasOne(Event::class)
            ->whereCode('PUB')->withDefault();
    }

    public function grant()
    {
        return $this->hasOne(Event::class)
            ->whereIn('code', ['GRT', 'REG'])->withDefault();
    }

    public function registration()
    {
        return $this->hasOne(Event::class)
            ->whereCode('REG')->withDefault();
    }

    public function entered()
    {
        return $this->hasOne(Event::class)
            ->whereCode('ENT')->withDefault();
    }

    /*public function status()
    {
        return $this->hasOne('Event::class')
            ->latest('event_date');
    }*/

    public function priority()
    {
        return $this->hasMany(Event::class)
            ->whereCode('PRI');
    }

    public function prioritiesFromView()
    {
        return $this->hasMany(EventLnkList::class, 'matter_id', 'id')
            ->where('code', 'PRI');
    }

    // All tasks, including renewals and done
    public function tasks()
    {
        return $this->hasManyThrough(Task::class, Event::class, 'matter_id', 'trigger_id', 'id');
    }

    // Pending excluding renewals
    public function tasksPending()
    {
        return $this->tasks()
            ->where('task.code', '!=', 'REN')
            ->whereDone(0)
            ->orderBy('due_date');
    }

    // Pending renewals
    public function renewalsPending()
    {
        return $this->tasks()
            ->where('task.code', 'REN')
            ->whereDone(0)
            ->orderBy('due_date');
    }

    // Returns all classifiers outside the "main display", including those inherited from the container (MatterClassifiers is a model referring to db view matter_classifiers)
    public function classifiers()
    {
        return $this->hasMany(MatterClassifiers::class)
            ->whereMainDisplay(0);
    }

    // Returns the classifiers native to the matter (only applies to a container, normally)
    public function classifiersNative()
    {
        return $this->hasMany(Classifier::class);
    }

    // Returns all classifiers of the "main display", including those inherited from the container (MatterClassifiers is a model referring to db view matter_classifiers)
    public function titles()
    {
        return $this->hasMany(MatterClassifiers::class)
            ->whereMainDisplay(1);
    }

    public function linkedBy()
    {
        return $this->belongsToMany(Matter::class, 'classifier', 'lnk_matter_id');
    }

    public function countryInfo()
    {
        return $this->belongsTo(Country::class, 'country');
    }

    public function originInfo()
    {
        return $this->belongsTo(Country::class, 'origin')->withDefault();
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function type()
    {
        return $this->belongsTo(Type::class)->withDefault();
    }

    public static function filter($sortkey = 'id', $sortdir = 'desc', $multi_filter = [], $display_with = false, $include_dead = false)
    {
        $query = Matter::select(
            'matter.uid AS Ref',
            'matter.country AS country',
            'matter.category_code AS Cat',
            'matter.origin',
            DB::raw("GROUP_CONCAT(DISTINCT event_name.name SEPARATOR '; ') AS Status"),
            DB::raw('MIN(status.event_date) AS Status_date'),
            DB::raw("GROUP_CONCAT(DISTINCT COALESCE(cli.display_name, clic.display_name, cli.name, clic.name) SEPARATOR '; ') AS Client"),
            DB::raw("GROUP_CONCAT(DISTINCT COALESCE(clilnk.actor_ref, cliclnk.actor_ref) SEPARATOR '; ') AS ClRef"),
            DB::raw("GROUP_CONCAT(DISTINCT COALESCE(app.display_name, app.name) SEPARATOR '; ') AS Applicant"),
            DB::raw("GROUP_CONCAT(DISTINCT COALESCE(agt.display_name, agtc.display_name, agt.name, agtc.name) SEPARATOR '; ') AS Agent"),
            DB::raw("GROUP_CONCAT(DISTINCT COALESCE(agtlnk.actor_ref, agtclnk.actor_ref) SEPARATOR '; ') AS AgtRef"),
            'tit1.value AS Title',
            DB::raw('COALESCE(tit2.value, tit1.value) AS Title2'),
            'tit3.value AS Title3',
            DB::raw("CONCAT_WS(' ', inv.name, inv.first_name) as Inventor1"),
            'fil.event_date AS Filed',
            'fil.detail AS FilNo',
            'pub.event_date AS Published',
            'pub.detail AS PubNo',
            DB::raw("COALESCE(grt.event_date, reg.event_date) AS Granted"),
            DB::raw("COALESCE(grt.detail, reg.detail) AS GrtNo"),
            'matter.id',
            'matter.container_id',
            'matter.parent_id',
            'matter.type_code',
            'matter.responsible',
            'del.login AS delegate',
            'matter.dead',
            DB::raw('isnull(matter.container_id) AS Ctnr')
        )->join(
            'matter_category', 
            'matter.category_code', 
            'matter_category.code'
        )->leftJoin(
            DB::raw('matter_actor_lnk clilnk JOIN actor cli ON cli.id = clilnk.actor_id'),
            function ($join) {
                $join->on('matter.id', 'clilnk.matter_id')->where('clilnk.role', 'CLI');
            }
        )->leftJoin(
            DB::raw('matter_actor_lnk cliclnk JOIN actor clic ON clic.id = cliclnk.actor_id'), 
            function ($join) {
                $join->on('matter.container_id', 'cliclnk.matter_id')->where(
                    [
                        ['cliclnk.role', 'CLI'],
                        ['cliclnk.shared', 1],
                    ]
                );
            }
        )->leftJoin(
            DB::raw('matter_actor_lnk agtlnk JOIN actor agt ON agt.id = agtlnk.actor_id'),
            function ($join) {
                $join->on('matter.id', 'agtlnk.matter_id')->where(
                    [
                        ['agtlnk.role', 'AGT'],
                        ['agtlnk.display_order', 1],
                    ]
                );
            }
        )->leftJoin(
            DB::raw('matter_actor_lnk agtclnk JOIN actor agtc ON agtc.id = agtclnk.actor_id'), 
            function ($join) {
                $join->on('matter.container_id', 'agtclnk.matter_id')->where(
                    [
                        ['agtclnk.role', 'AGT'],
                        ['agtclnk.shared', 1],
                    ]
                );
            }
        )->leftJoin(
            DB::raw('matter_actor_lnk applnk JOIN actor app ON app.id = applnk.actor_id'),
            function ($join) {
                $join->on(DB::raw('ifnull(matter.container_id, matter.id)'), 'applnk.matter_id')->where('applnk.role', 'APP');
            }
        )->leftJoin(
            DB::raw('matter_actor_lnk dellnk JOIN actor del ON del.id = dellnk.actor_id'),
            function ($join) {
                $join->on(DB::raw('ifnull(matter.container_id, matter.id)'), 'dellnk.matter_id')->where('dellnk.role', 'DEL');
            }
        )->leftJoin(
            'event AS fil', 
            function ($join) {
                $join->on('matter.id', 'fil.matter_id')->where('fil.code', 'FIL');
            }
        )->leftJoin(
            'event AS pub', 
            function ($join) {
                $join->on('matter.id', 'pub.matter_id')->where('pub.code', 'PUB');
            }
        )->leftJoin(
            'event AS grt', 
            function ($join) {
                $join->on('matter.id', 'grt.matter_id')->where('grt.code', 'GRT');
            }
        )->leftJoin(
            'event AS reg', 
            function ($join) {
                $join->on('matter.id', 'reg.matter_id')->where('reg.code', 'REG');
            }
        )->leftJoin(
            DB::raw('event status JOIN event_name ON event_name.code = status.code AND event_name.status_event = 1'), 
            'matter.id', 
            'status.matter_id'
        )->leftJoin(
            DB::raw('event e2 JOIN event_name en2 ON e2.code = en2.code AND en2.status_event = 1'),
            function ($join) {
                $join->on('status.matter_id', 'e2.matter_id')->whereColumn('status.event_date', '<', 'e2.event_date');
            }
        )->leftJoin(
            DB::raw(
                'classifier tit1 JOIN classifier_type ct1 
                ON tit1.type_code = ct1.code 
                AND ct1.main_display = 1 
                AND ct1.display_order = 1'
            ), 
            DB::raw('IFNULL(matter.container_id, matter.id)'), 
            'tit1.matter_id'
        )->leftJoin(
            DB::raw(
                'classifier tit2 JOIN classifier_type ct2 
                ON tit2.type_code = ct2.code 
                AND ct2.main_display = 1 
                AND ct2.display_order = 2'
            ), 
            DB::raw('IFNULL(matter.container_id, matter.id)'), 
            'tit2.matter_id'
        )->leftJoin(
            DB::raw(
                'classifier tit3 JOIN classifier_type ct3 
                ON tit3.type_code = ct3.code 
                AND ct3.main_display = 1 
                AND ct3.display_order = 3'
            ), 
            DB::raw('IFNULL(matter.container_id, matter.id)'), 
            'tit3.matter_id'
        )->where('e2.matter_id', null);

        if (array_key_exists('Inventor1', $multi_filter)) {
            $query->leftJoin(
                DB::raw('matter_actor_lnk invlnk JOIN actor inv ON inv.id = invlnk.actor_id'),
                function ($join) {
                    $join->on(DB::raw('ifnull(matter.container_id, matter.id)'), 'invlnk.matter_id')->where('invlnk.role', 'INV');
                }
            );
        } else {
            $query->leftJoin(
                DB::raw('matter_actor_lnk invlnk JOIN actor inv ON inv.id = invlnk.actor_id'),
                function ($join) {
                    $join->on(DB::raw('ifnull(matter.container_id, matter.id)'), 'invlnk.matter_id')->where(
                        [
                            ['invlnk.role', 'INV'],
                            ['invlnk.display_order', 1],
                        ]
                    );
                }
            );
        }

        $authUserRole = Auth::user()->default_role;
        $authUserId = Auth::user()->id;

        if ($display_with) {
            $query->where('matter_category.display_with', $display_with);
        }

        // When the user is a client or no role is defined, limit the matters to client's own matters
        if ($authUserRole == 'CLI' || empty($authUserRole)) {
            $query->where(
                function ($q) use ($authUserId) {
                    $q->where('cli.id', $authUserId)
                        ->orWhere('clic.id', $authUserId);
                }
            );
        }

        if (! empty($multi_filter)) {
            // When no filters are set, sorting is done by descending matter id's to see the most recent matters first.
            // As soon as a filter is set, sorting is done by default by caseref instead of by id, ascending.
            if ($sortkey == 'id') {
                $sortkey = 'caseref';
                $sortdir = 'asc';
            }
            foreach ($multi_filter as $key => $value) {
                if ($value != '') {
                    switch ($key) {
                        case 'Ref':
                            $query->where(function ($q) use ($value) {
                                $q->where('uid', 'LIKE', "$value%")
                                    ->orWhere('alt_ref', 'LIKE', "$value%");
                            });
                            break;
                        case 'Cat':
                            $query->where('category_code', 'LIKE', "$value%");
                            break;
                        case 'country':
                            $query->where('matter.country', 'LIKE', "$value%");
                            break;
                        case 'Status':
                            $query->where('event_name.name', 'LIKE', "$value%");
                            break;
                        case 'Status_date':
                            $query->where('status.event_date', 'LIKE', "$value%");
                            break;
                        case 'Client':
                            $query->where(DB::raw('IFNULL(cli.name, clic.name)'), 'LIKE', "$value%");
                            break;
                        case 'ClRef':
                            $query->where(DB::raw('IFNULL(clilnk.actor_ref, cliclnk.actor_ref)'), 'LIKE', "$value%");
                            break;
                        case 'Applicant':
                            $query->where('app.name', 'LIKE', "$value%");
                            break;
                        case 'Agent':
                            $query->where(DB::raw('IFNULL(agt.name, agtc.name)'), 'LIKE', "$value%");
                            break;
                        case 'AgtRef':
                            $query->where(DB::raw('IFNULL(agtlnk.actor_ref, agtclnk.actor_ref)'), 'LIKE', "$value%");
                            break;
                        case 'Title':
                            $query->where(DB::Raw('concat_ws(" ", tit1.value, tit2.value, tit3.value)'), 'LIKE', "%$value%");
                            break;
                        case 'Inventor1':
                            $query->where('inv.name', 'LIKE', "$value%");
                            break;
                        case 'Filed':
                            $query->where('fil.event_date', 'LIKE', "$value%");
                            break;
                        case 'FilNo':
                            $query->where('fil.detail', 'LIKE', "$value%");
                            break;
                        case 'Published':
                            $query->where('pub.event_date', 'LIKE', "$value%");
                            break;
                        case 'PubNo':
                            $query->where('pub.detail', 'LIKE', "$value%");
                            break;
                        case 'Granted':
                            $query->where('grt.event_date', 'LIKE', "$value%")
                                ->orWhere('reg.event_date', 'LIKE', "$value%");
                            break;
                        case 'GrtNo':
                            $query->where('grt.detail', 'LIKE', "$value%")
                                ->orWhere('reg.detail', 'LIKE', "$value%");
                            break;
                        case 'responsible':
                            $query->whereRaw("'$value' IN (matter.responsible, del.login)");
                            break;
                        case 'Ctnr':
                            if ($value) {
                                $query->whereNull('container_id');
                            }
                            break;
                        default:
                            $query->where($key, 'LIKE', "$value%");
                            break;
                    }
                }
            }
        }

        // Do not display dead families unless desired
        if (! $include_dead) {
            $query->whereRaw('(select count(1) from matter m where m.caseref = matter.caseref and m.dead = 0) > 0');
        }

        // Sorting by caseref is special - set additional conditions here
        if ($sortkey == 'caseref') {
            $query->groupBy('matter.caseref', 'matter.container_id', 'matter.suffix', 'fil.event_date');
            if ($sortdir == 'desc') {
                $query->orderByDesc('matter.caseref');
            }
        } else {
            $query->groupBy($sortkey, 'matter.caseref', 'matter.container_id', 'matter.suffix', 'fil.event_date')
                ->orderBy($sortkey, $sortdir);
        }

        return $query;
    }

    public static function getCategoryMatterCount($user = null)
    {
        $authUserRole = Auth::user()->default_role;
        $authUserId = Auth::user()->id;
        $query = Matter::leftJoin('matter_category as mc', 'mc.code', 'matter.category_code')
            ->groupBy('category_code', 'category')
            ->select('mc.category', 'category_code', DB::raw('count(*) as total'));
        if ($authUserRole == 'CLI' || empty($authUserRole)) {
            $query->join('matter_actor_lnk as cli', 'cli.matter_id', DB::raw('ifnull(matter.container_id, matter.id)'))
                ->where([['cli.role', 'CLI'], ['cli.actor_id', $authUserId]]);
        } else {
            if ($user) {
                $query = $query->where('responsible', '=', $user);
            }
        }

        return $query->get();
    }

    public function getDescription($lang = 'en')
    {
        $description = [];
        //$matter = Matter::find($id);
        $filed_date = Carbon::parse($this->filing->event_date);
        // "grant" includes registration (for trademarks)
        $granted_date = Carbon::parse($this->grant->event_date);
        $published_date = Carbon::parse($this->publication->event_date);
        $title = $this->titles->where('type_code', 'TITOF')->first()->value 
            ?? $this->titles->first()->value
            ?? "";
        $title_EN = $this->titles->where('type_code', 'TITEN')->first()->value 
            ?? $this->titles->first()->value
            ?? "";
        if ($lang == 'fr') {
            $description[] = "N/réf : {$this->uid}";
            if ($this->client->actor_ref) {
                $description[] = "V/réf : {$this->client->actor_ref}";
            }
            if ($this->category_code == 'PAT') {
                if ($granted_date) {
                    $description[] = "Brevet {$this->grant->detail} déposé en {$this->countryInfo->name_FR} le {$filed_date->locale('fr_FR')->isoFormat('LL')} et délivré le {$granted_date->locale('fr_FR')->isoFormat('LL')}";
                } else {
                    $line = "Demande de brevet {$this->filing->detail} déposée en {$this->countryInfo->name_FR} le {$filed_date->locale('fr_FR')->isoFormat('LL')}";
                    if ($published_date) {
                        $line .= " et publiée le {$published_date->locale('fr_FR')->isoFormat('LL')} sous le n°{$this->publication->detail}";
                    }
                    $description[] = $line;
                }
                $description[] = "Pour : $title";
                $description[] = "Au nom de : {$this->applicants->pluck('name')->join(', ')}";
            }
            if ($this->category_code == 'TM') {
                $line = "Marque {$this->filing->detail} déposée en {$this->countryInfo->name_FR} le {$filed_date->locale('fr_FR')->isoFormat('LL')}";
                if ($published_date) {
                    $line .= ", publiée le {$published_date->locale('fr_FR')->isoFormat('LL')} sous le n°{$this->publication->detail}";
                }
                if ($granted_date) {
                    $line .=  " et enregistrée le {$granted_date->locale('fr_FR')->isoFormat('LL')}";
                }
                $description[] = $line;
                $description[] = "Pour : $title";
                $description[] = "Au nom de : {$this->applicants->pluck('name')->join(', ')}";
            }
        }
        if ($lang == 'en') {
            $description[] = "Our ref: {$this->uid}";
            if ($this->client->actor_ref) {
                $description[] = "Your ref: {$this->client->actor_ref}";
            }
            if ($this->category_code == 'PAT') {
                if ($granted_date) {
                    $description[] = "Patent {$this->grant->detail} filed in {$this->countryInfo->name} on {$filed_date->locale('en_US')->isoFormat('LL')} and granted on {$granted_date->locale('en_US')->isoFormat('LL')}";
                } else {
                    $description[] = "Patent application {$this->filing->detail} filed in {$this->countryInfo->name} on {$filed_date->locale('en_US')->isoFormat('LL')}";
                    if ($published_date) {
                        $description[]= " and published on {$published_date->locale('en_US')->isoFormat('LL')} as {$this->publication->detail}";
                    }
                }
                $description[] = "For: $title_EN" ;
                $description[] = "In name of: {$this->applicants->pluck('name')->join(', ')}";
            }
            if ($this->category_code == 'TM') {
                $line = "Trademark {$this->filing->detail} filed in {$this->countryInfo->name_FR} on {$filed_date->locale('en_US')->isoFormat('LL')}";
                if ($published_date) {
                    $line .= ", published on {$published_date->locale('en_US')->isoFormat('LL')} as {$this->publication->detail}";
                }
                if ($granted_date) {
                    $line .=  " and registered on {$granted_date->locale('en_US')->isoFormat('LL')}";
                }
                $description[] = $line;
                $description[] = "For: $title_EN";
                $description[] = "In name of: {$this->applicants->pluck('name')->join(', ')}";
            }
        }
        return $description;
    }

    public function getBillingAddress()
    {
        $client = $this->client->actor ?? $this->sharedClient->actor;

        if($client && $client->address_billing) {
            return collect([
                collect([
                    $this->payer->actor?->name,
                    $this->sharedPayer->actor?->name,
                ]),
                collect([
                    $this->payer->actor?->address,
                    $this->sharedPayer->actor?->address,
                    $this->client->actor?->address_billing,
                    $this->sharedClient->actor?->address_billing
                ]),
                collect([
                    $this->payer->actor?->country,
                    $this->sharedPayer->actor?->country,
                    $this->client->actor?->country_billing,
                    $this->sharedClient->actor?->country_billing
                ]),
            ])->map(function ($element) {
                return $element->filter()->unique()->first();
            })->filter()->implode("\n");
        }

        return collect([
            collect([
                $this->payer->actor?->name,
                $this->sharedPayer->actor?->name,
                $this->client->actor?->name,
                $this->sharedClient->actor?->name
            ]),
            collect([
                $this->payer->actor?->address,
                $this->sharedPayer->actor?->address,
                $this->client->actor?->address,
                $this->sharedClient->actor?->address
            ]),
            collect([
                $this->payer->actor?->country,
                $this->sharedPayer->actor?->country,
                $this->client->actor?->country,
                $this->sharedClient->actor?->country
            ])
        ])->map(function ($element) {
            return $element->filter()->unique()->first();
        })->filter()->implode("\n");
    }

    /**
     * Get the name of the owner or applicant of the current matter
     * Used for the document merge
     *
     * @return string|null
     */
    public function getOwnerName()
    {
        $owners = $this->sharedOwners->pluck('name')->merge($this->owners->pluck('name'))->unique()->sort();
        $applicants = $this->sharedApplicantsFromLnk->pluck('name')->merge($this->applicantsFromLnk->pluck('name'))->unique()->sort();

        if ($owners->isNotEmpty()) {
            return $owners->implode("\n");
        }

        return $applicants->implode("\n");
    }
}
