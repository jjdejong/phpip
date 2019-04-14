<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class Matter extends Model
{
    protected $table = 'matter';
    public $timestamps = false; // removes timestamp updating in this table (done via MySQL triggers)
    protected $hidden = ['creator', 'updated', 'updater'];
    protected $guarded = ['id', 'creator', 'updated', 'updater'];

    // use \Venturecraft\Revisionable\RevisionableTrait;
    // protected $revisionEnabled = true;
    // protected $revisionCreationsEnabled = true;
    // protected $revisionCleanup = true; //Remove old revisions (works only when used with $historyLimit)
    // protected $historyLimit = 500; //Maintain a maximum of 500 changes at any point of time, while cleaning up old revisions.

    protected $appends = ['uid']; // Allows eager loading of uid

    public function getUidAttribute()
    {
        return $this->caseref . $this->suffix;
    }

    public function family()
    { // Gets other family members (where clause is ignored by eager loading)
        return $this->hasMany('App\Matter', 'caseref', 'caseref')
                        ->where('id', '!=', $this->id)
                        ->orderBy('origin')
                        ->orderBy('country');
    }

    public function container()
    {
        return $this->belongsTo('App\Matter', 'container_id');
    }

    public function parent()
    {
        return $this->belongsTo('App\Matter', 'parent_id');
    }

    public function children()
    {
        return $this->hasMany('App\Matter', 'parent_id')
                        ->orderBy('origin')
                        ->orderBy('country');
    }

    public function priorityTo()
    { // Gets external matters claiming priority on this one (where clause is ignored by eager loading)
        return $this->belongsToMany('App\Matter', 'event', 'alt_matter_id')
                        ->where('caseref', '!=', $this->caseref)
                        ->orderBy('caseref')
                        ->orderBy('origin')
                        ->orderBy('country');
    }

    public function actors()
    {
        // MatterActors refers to a view that also includes the actors inherited from the container. Can only be used to display data
        return $this->hasMany('App\MatterActors');
    }

    public function actorPivot()
    {
        return $this->hasMany('App\ActorPivot');
    }

    public function events()
    {
        return $this->hasMany('App\Event')
                        ->orderBy('event_date');
    }

    public function filing()
    {
        return $this->hasOne('App\Event')
                        ->where('code', 'FIL');
    }

    public function publication()
    {
        return $this->hasOne('App\Event')
                        ->where('code', 'PUB');
    }

    public function grant()
    {
        return $this->hasOne('App\Event')
                        ->where('code', 'GRT');
    }

    public function status()
    {
        return $this->hasOne('App\Event')
                        ->latest('event_date');
    }

    public function priority()
    {
        return $this->hasMany('App\Event')
                        ->where('code', 'PRI');
    }

    public function tasksPending()
    { // Excludes renewals
        return $this->hasManyThrough('App\Task', 'App\Event', 'matter_id', 'trigger_id', 'id')
                        ->where('task.code', '!=', 'REN')
                        ->where('done', 0)
                        ->orderBy('due_date');
    }

    public function renewalsPending()
    {
        return $this->hasManyThrough('App\Task', 'App\Event', 'matter_id', 'trigger_id', 'id')
                        ->where('task.code', 'REN')
                        ->where('done', 0)
                        ->orderBy('due_date');
    }

    public function classifiers()
    {
        return $this->hasMany('App\MatterClassifiers')
                        ->where('main_display', 0);
    }

    public function classifiersNative()
    {
        return $this->hasMany('App\Classifier');
    }

    public function titles()
    {
        return $this->hasMany('App\MatterClassifiers')
                        ->where('main_display', 1);
    }

    public function linkedBy()
    {
        return $this->belongsToMany('App\Matter', 'classifier', 'lnk_matter_id');
    }

    public function countryInfo()
    {
        return $this->belongsTo('App\Country', 'country');
    }

    public function originInfo()
    {
        return $this->belongsTo('App\Country', 'origin');
    }

    public function category()
    {
        return $this->belongsTo('App\Category');
    }

    public function type()
    {
        return $this->belongsTo('App\Type');
    }

    public static function filter($sortkey = 'caseref', $sortdir = 'asc', $multi_filter = [], $display_with = false, $paginated = false)
    {
        $query = Matter::select(
            DB::raw("CONCAT_WS('', caseref, suffix) AS Ref"),
                'matter.country AS country',
                'matter.category_code AS Cat',
                'matter.origin',
                'event_name.name AS Status',
                'status.event_date AS Status_date',
                DB::raw("COALESCE(cli.display_name, clic.display_name, cli.name, clic.name) AS Client"),
                DB::raw("COALESCE(clilnk.actor_ref, lclic.actor_ref) AS ClRef"),
                DB::raw("COALESCE(app.display_name, app.name) AS Applicant"),
                DB::raw("COALESCE(agt.display_name, agt.name) AS Agent"),
                'agtlnk.actor_ref AS AgtRef',
                'classifier.value AS Title',
                'classifier2.value AS Title2',
                DB::raw("CONCAT_WS(' ', inv.name, inv.first_name) as Inventor1"),
                'fil.event_date AS Filed',
                'fil.detail AS FilNo',
                'pub.event_date AS Published',
                'pub.detail AS PubNo',
                'grt.event_date AS Granted',
                'grt.detail AS GrtNo',
                'matter.id',
                'matter.container_id',
                'matter.parent_id',
                'matter.responsible',
                'del.login AS delegate',
                'matter.dead',
                DB::raw("IF(isnull(matter.container_id),1,0) AS Ctnr")
        )
        ->join('matter_category', 'matter.category_code', 'matter_category.code')
        ->leftJoin(
            DB::raw('matter_actor_lnk clilnk
            JOIN actor cli ON cli.id = clilnk.actor_id'),
            function ($join) {
                $join->on('matter.id', 'clilnk.matter_id')->where('clilnk.role', 'CLI');
            }
        )
        ->leftJoin(
            DB::raw('matter_actor_lnk lclic
            JOIN actor clic ON clic.id = lclic.actor_id'),
            function ($join) {
                $join->on('matter.container_id', 'lclic.matter_id')->where([
                    ['lclic.role', 'CLI'],
                    ['lclic.shared', 1]
                ]);
            }
        );

        if (array_key_exists('Inventor1', $multi_filter)) {
            $query->leftJoin(
                DB::raw('matter_actor_lnk invlnk
                JOIN actor inv ON inv.id = invlnk.actor_id'),
                function ($join) {
                    $join->on(DB::raw('ifnull(matter.container_id, matter.id)'), 'invlnk.matter_id')->where('invlnk.role', 'INV');
                }
            );
        } else {
            $query->leftJoin(
                DB::raw('matter_actor_lnk invlnk
                JOIN actor inv ON inv.id = invlnk.actor_id'),
                function ($join) {
                    $join->on(DB::raw('ifnull(matter.container_id, matter.id)'), 'invlnk.matter_id')->where([
                        ['invlnk.role', 'INV'],
                        ['invlnk.display_order', 1]
                    ]);
                }
            );
        }

        $query->leftJoin(
            DB::raw('matter_actor_lnk agtlnk
            JOIN actor agt ON agt.id = agtlnk.actor_id'),
            function ($join) {
                $join->on('matter.id', 'agtlnk.matter_id')->where([
                    ['agtlnk.role', 'AGT'],
                    ['agtlnk.display_order', 1]
                ]);
            }
        )
        ->leftJoin(
            DB::raw('matter_actor_lnk applnk
            JOIN actor app ON app.id = applnk.actor_id'),
            function ($join) {
                $join->on('matter.id', 'applnk.matter_id')->where([
                    ['applnk.role', 'APP'],
                    ['applnk.display_order', 1]
                ]);
            }
        )
        ->leftJoin(
            DB::raw('matter_actor_lnk dellnk
            JOIN actor del ON del.id = dellnk.actor_id'),
            function ($join) {
                $join->on(DB::raw('ifnull(matter.container_id,matter.id)'), 'dellnk.matter_id')->where('dellnk.role', 'DEL');
            }
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
        ->leftJoin(DB::raw('event status
            JOIN event_name ON event_name.code = status.code AND event_name.status_event = 1'), 'matter.id', 'status.matter_id')
        ->leftJoin(
            DB::raw('event e2
            JOIN event_name en2 ON e2.code=en2.code AND en2.status_event = 1'),
            function ($join) {
                $join->on('status.matter_id', 'e2.matter_id')->whereColumn('status.event_date', '<', 'e2.event_date');
            }
        )
        ->leftJoin(DB::raw('classifier
            JOIN classifier_type ON classifier.type_code = classifier_type.code AND classifier_type.main_display = 1 AND classifier_type.display_order = 1'), DB::raw('IFNULL(matter.container_id, matter.id)'), 'classifier.matter_id')
        ->leftJoin(DB::raw('classifier classifier2
            JOIN classifier_type ct2 ON classifier2.type_code = ct2.code AND ct2.main_display = 1 AND ct2.display_order = 2'), DB::raw('IFNULL(matter.container_id, matter.id)'), 'classifier2.matter_id')
        ->where('e2.matter_id', null);

        $authUserRole = Auth::user()->default_role;
        $authUserId = Auth::user()->id;

        if ($display_with) {
            $query->where('matter_category.display_with', $display_with);
        }
        if ($authUserRole == 'CLI') {
            $query->whereRaw($authUserId . ' IN (cli.id, clic.id)');
        }

        if (!empty($multi_filter)) {
            foreach ($multi_filter as $key => $value) {
                if ($value != '' && $key != 'display' && $key != 'display_style') {
                    if ($key == 'responsible') {
                        $query->whereRaw("'$value' IN (matter.responsible, del.login)");
                    } else {
                        $query->having("$key", 'LIKE', "$value%");
                    }
                }
            }
        }

        if ($sortkey == 'caseref') {
            if ($sortdir == 'desc') {
                $query->orderByRaw('matter.caseref DESC, matter.container_id, matter.origin, matter.country, matter.type_code, matter.idx');
            } else {
                $query->orderByRaw('matter.caseref, matter.container_id, matter.origin, matter.country, matter.type_code, matter.idx');
            }
        } else {
            $query->orderByRaw("$sortkey $sortdir, matter.caseref, matter.origin, matter.country");
        }

        if ($paginated) {
            $matters = $query->simplePaginate(25);
        } else {
            $matters = $query->get();
        }

        /* \Event::listen('Illuminate\Database\Events\QueryExecuted', function($query) {
          var_dump($query->sql);
          var_dump($query->bindings);
          }); */

        return $matters;
    }
    public static function getCategoryMatterCount($user = null)
    {
        $authUserRole = Auth::user()->default_role;
        $authUserId = Auth::user()->id;
        $query = Matter::leftJoin('matter_category as mc', 'mc.code', '=', 'matter.category_code')
                ->groupBy('category_code', 'category')
                ->select('mc.category', 'category_code', DB::raw('count(*) as total'));
        if ($authUserRole == 'CLI') {
            $query->join('matter_actor_lnk as cli', DB::raw("ifnull(matter.container_ID,matter.ID)"), '=', 'cli.matter_ID')
            ->where([[ 'cli.role','CLI'],['cli.actor_id', $authUserId]]);
        } else {
            if ($user) {
                $query = $query->where('responsible', '=', $user);
            }
        }
        return $query->get();
    }
    public static function getDescription($id, $lang='en') {
        $query = Matter::select(DB::raw("CONCAT_WS('', caseref, suffix) AS Ref"),
                'matter.country AS country',
                'matter.category_code AS Cat',
                'matter.origin',
                'event_name.name AS Status',
                'status.event_date AS Status_date',
                DB::raw ( "COALESCE(cli.display_name, clic.display_name, cli.name, clic.name) AS Client" ),
                DB::raw ( "COALESCE(clilnk.actor_ref, lclic.actor_ref) AS ClRef" ),
                DB::raw ( "COALESCE(app.display_name, app.name) AS Applicant" ),
                //DB::raw ( "COALESCE(agt.display_name, agt.name) AS Agent" ),
                'agtlnk.actor_ref AS AgtRef',
                'classifier.value AS Title',
                'classifier2.value AS Title2',
                DB::raw ( "CONCAT_WS(' ', inv.name, inv.first_name) as Inventor1" ),
                'fil.event_date AS Filed',
                'fil.detail AS FilNo',
                'pub.event_date AS Published',
                'pub.detail AS PubNo',
                'grt.event_date AS Granted',
                'grt.detail AS GrtNo',
                //'matter.id',
                //'matter.container_id',
                //'matter.parent_id',
                //'matter.responsible',
                //'del.login AS delegate',
                //'matter.dead',
                'country.name AS country_name',
                'country.name_FR AS country_name_FR',
                'country.name_DE AS country_name_DE',
                DB::raw ( "IF(isnull(matter.container_id),1,0) AS Ctnr" ))
        ->join('matter_category', 'matter.category_code', 'matter_category.code')
        ->join('country', 'matter.country', 'country.iso')
        ->leftJoin(DB::raw('matter_actor_lnk clilnk
            JOIN actor cli ON cli.id = clilnk.actor_id'), function ($join) {
                $join->on('matter.id', 'clilnk.matter_id')->where('clilnk.role', 'CLI');
            }
        )
        ->leftJoin(DB::raw('matter_actor_lnk lclic
            JOIN actor clic ON clic.id = lclic.actor_id'), function ($join) {
                $join->on('matter.container_id', 'lclic.matter_id')->where([
                    ['lclic.role', 'CLI'],
                    ['lclic.shared', 1]
                ]);
            }
        );

        $query->leftJoin(DB::raw('matter_actor_lnk invlnk
                JOIN actor inv ON inv.id = invlnk.actor_id'), function ($join) {
                    $join->on(DB::raw('ifnull(matter.container_id, matter.id)'), 'invlnk.matter_id')->where([
                        ['invlnk.role', 'INV'],
                        ['invlnk.display_order', 1]
                    ]);
                }
            );

        $query->leftJoin(DB::raw('matter_actor_lnk agtlnk
            JOIN actor agt ON agt.id = agtlnk.actor_id'), function ($join) {
                $join->on('matter.id', 'agtlnk.matter_id')->where([
                    ['agtlnk.role', 'AGT'],
                    ['agtlnk.display_order', 1]
                ]);
            }
        )
        ->leftJoin(DB::raw('matter_actor_lnk applnk
            JOIN actor app ON app.id = applnk.actor_id'), function ($join) {
                $join->on('matter.id', 'applnk.matter_id')->where([
                    ['applnk.role', 'APP'],
                    ['applnk.display_order', 1]
                ]);
            }
        )
        ->leftJoin(DB::raw('matter_actor_lnk dellnk
            JOIN actor del ON del.id = dellnk.actor_id'), function ($join) {
                $join->on(DB::raw('ifnull(matter.container_id,matter.id)'), 'dellnk.matter_id')->where('dellnk.role', 'DEL');
            }
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
        ->leftJoin(DB::raw('event status
            JOIN event_name ON event_name.code = status.code AND event_name.status_event = 1'), 'matter.id', 'status.matter_id')
        ->leftJoin(DB::raw('event e2
            JOIN event_name en2 ON e2.code=en2.code AND en2.status_event = 1'), function ($join) {
                $join->on('status.matter_id', 'e2.matter_id')->whereColumn('status.event_date', '<', 'e2.event_date');
            }
        )
        ->leftJoin(DB::raw('classifier
            JOIN classifier_type ON classifier.type_code = classifier_type.code AND classifier_type.main_display = 1 AND classifier_type.display_order = 1'), DB::raw('IFNULL(matter.container_id, matter.id)'), 'classifier.matter_id')
        ->leftJoin(DB::raw('classifier classifier2
            JOIN classifier_type ct2 ON classifier2.type_code = ct2.code AND ct2.main_display = 1 AND ct2.display_order = 2'), DB::raw('IFNULL(matter.container_id, matter.id)'), 'classifier2.matter_id')
        ->where('matter.id','=',$id);
        $info = $query->first();
        $description = array();
        $filed_date = date_create($info['Filed']);
        $granted_date = date_create($info['Granted']);
        $published_date = date_create($info['Published']);
        if($lang == "fr") {
            $description[] = "N/réf : " . $info['Ref'] ;
            if($info['ClRef']) {$description[] = "V/réf : " . $info['ClRef'] ;}
            if ($info['Cat'] == 'PAT') {
                if ($info['Granted']) {
                    $description[] = "Brevet " . $info['GrtNo'] . " déposé en " . $info['country_name_FR'] . " le " . $filed_date->format("d/m/Y") . " et délivré le " . $granted_date->format("d/m/Y");
                }
                else {
                    $line = "Demande de brevet n°" . $info['FilNo'] . " déposée en " . $info['country_name_FR'] . " le ". $filed_date->format("d/m/Y");
                    if($info['Published']) {$line .= " et publiée le " . $published_date->format("d/m/Y") ." sous le n° ". $info['PubNo'];}
                    $description[] = $line;
                }
                $description[] = "Pour : " . $info['Title1'] ;
                $description[] = "Au nom de : ". $info['Applicant'] ;
            }
            if ($info['Cat'] == 'TM') {
                $line = "Marque n° " . $info['FilNo'] . " déposée en " . $info['country_name_FR'] . " le " . $filed_date->format("d/m/Y") ;
                if($info['Published']) {$line .= ", publiée le " . $published_date->format("d/m/Y") ." sous le n° ". $info['PubNo'];}
                if ($info['Granted']) {
                    $line .=  " et enregistrée le " . $granted_date->format("d/m/Y");
                }
                $description[] = $line;
                $description[] = "Pour : " . $info['Title1'] ;
                $description[] = "Au nom de : ". $info['Applicant'] ;
            }
        }
        if($lang == "en") {
            $description[] = "Our ref: " . $info['Ref'] ;
            if($info['ClRef']) {$description[] = "Your ref: " . $info['ClRef'] ;}
            if ($info['Cat'] == 'PAT') {
                if ($info['Granted']) {
                    $description[] = "Patent " . $info['FilNo'] . " filed in " . $info['country_name'] . " at " . $info['Filed'] . $info['GrtNo'] . " and granted at " . $info['Granted'];
                }
                else {
                    $description[] = "Patent application n°" . $info['FilNo'] . " filed in " . $info['country_name'] . " at ". $info['Filed'];
                    if($info['Published']) {$description[]= " and published at " . $info['Published'] ." with no ". $info['PubNo'];}
                }
                $description[] = "For: " . $info['Title1'] ;
                $description[] = "In name of: ". $info['Applicant'] ;
            }
            if ($info['Cat'] == 'TM') {
                $line = "Marque no " . $info['FilNo'] . " filed in " . $info['country_name_FR'] . " at " . $info['Filed'] ;
                if($info['Published']) {$line .= ", published at " .  $info['Published'] ." with no ". $info['PubNo'];}
                if ($info['Granted']) {
                    $line .=  " and registered at " . $info['Granted'];
                }
                $description[] = $line;
                $description[] = "For: " . $info['Title1'] ;
                $description[] = "In name of: ". $info['Applicant'] ;
            }
        }
    return $description;
    }
}
