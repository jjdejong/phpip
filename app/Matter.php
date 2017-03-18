<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class Matter extends Model {
	protected $table = 'matter';
	public $timestamps = false; // removes timestamp updating in this table (done via MySQL triggers)
	protected $hidden = ['creator', 'updated', 'updater'];

	public function family() // Gets other family members (where clause is ignored by eager loading)
	{
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
	
	public function priorityTo() // Gets external matters claiming priority on this one (where clause is ignored by eager loading)
	{
		return $this->belongsToMany('App\Matter', 'event', 'alt_matter_id')
		->where('caseref', '!=', $this->caseref)
		->orderBy('caseref')
		->orderBy('origin')
		->orderBy('country');
	}
	
	/*public function actors() {
		return $this->belongsToMany('App\Actor', 'matter_actor_lnk')
		->withPivot('id', 'role', 'display_order', 'shared', 'actor_ref', 'company_id', 'rate', 'date');
	}*/
	
	public function roles() {
		return $this->belongsToMany('App\Role', 'matter_actor_lnk', 'matter_id', 'role')
		->withPivot('id', 'role', 'shared');
	}
	
	public function actors()
	{
		$actors = DB::table('matter_actor_lnk as ma')
			->select( DB::raw ( "COALESCE(actor.display_name, CONCAT_WS(' ', actor.name, actor.first_name)) as name" ), 
					'ar.name as role_name', 
					'ma.actor_id',
					'ma.role',
					'ma.shared',
					'ma.actor_ref',
					'ma.company_id',
					'actor.company_id as default_company_id',
					'actor.warn',
					'ma.date',
					'ma.rate',
					'ar.display_order as role_order',
					'ma.display_order',
					'ar.show_ref',
					'ar.show_company',
					'ar.show_rate',
					'ar.show_date',
					'ma.id',
					DB::raw ("IF(ma.matter_id = '$this->container_id', 1, 0) AS inherited"))
			->where('matter_id', $this->id)
			->orWhere(function ($query) {
            	$query->where('matter_id', $this->container_id)
                	->where('ma.shared', 1);
            })
			->join('actor', 'actor.id', 'ma.actor_id')
			->join('actor_role as ar', 'ar.code', 'ma.role')
            ->orderBy('ar.display_order')->orderBy('ma.display_order');
		return $actors->get();
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
		/*\Event::listen('Illuminate\Database\Events\QueryExecuted', function($query) {
		 var_dump($query->sql);
		 var_dump($query->bindings);
		 });*/
		return $this->hasOne('App\Event')
		->latest('event_date');
	}
	
	public function priority()
	{
		return $this->hasMany('App\Event')
		->where('code', 'PRI');
	}
	
	public function tasks() // Excludes renewals 
	{
		return $this->hasManyThrough('App\Task', 'App\Event', 'matter_id', 'trigger_id', 'id')
			->where('task.code', '!=', 'REN')
			->orderBy('due_date');
	}
	
	public function tasksPending() // Excludes renewals
	{
		return $this->hasManyThrough('App\Task', 'App\Event', 'matter_id', 'trigger_id', 'id')
		->where('task.code', '!=', 'REN')
		->where('done', 0)
		->orderBy('due_date');
	}
	
	public function renewals()
	{
		return $this->hasManyThrough('App\Task', 'App\Event', 'matter_id', 'trigger_id', 'id')
		->where('task.code', 'REN')
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
			return $this->hasMany('App\Classifier');
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
	
	public function filter ($sortField = 'caseref', $sortDir = 'asc', $multi_filter = [], $matter_category_display_type = false, $paginated = false) 
	{
		$query = $this->select ( DB::raw ( "CONCAT_WS('', CONCAT_WS('-', CONCAT_WS('/', concat(caseref, matter.country), origin), matter.type_code), idx) AS Ref" ),
			'matter.country AS country',
			'matter.category_code AS Cat',
			'matter.origin',
			'event_name.name AS Status',
			'status.event_date AS Status_date',
			DB::raw ( "COALESCE(cli.display_name, clic.display_name, cli.name, clic.name) AS Client" ),
			DB::raw ( "COALESCE(clilnk.actor_ref, lclic.actor_ref) AS ClRef" ),
			DB::raw ( "COALESCE(app.display_name, app.name) AS Applicant" ),
			DB::raw ( "COALESCE(agt.display_name, agt.name) AS Agent" ),
			'agtlnk.actor_ref AS AgtRef',
			'classifier.value AS Title',
			DB::raw ( "CONCAT_WS(' ', inv.name, inv.first_name) as Inventor1" ),
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
			DB::raw ( "IF(isnull(matter.container_id),1,0) AS Ctnr" ));
		
		$query->join ( 'matter_category', 'matter.category_code', 'matter_category.code' );
		$query->leftJoin ( DB::raw ( 'matter_actor_lnk clilnk
			JOIN actor cli ON cli.id = clilnk.actor_id' ), function ($join) {
			$join->on ( 'matter.id', 'clilnk.matter_id' )->where ( 'clilnk.role', 'CLI' );
		} );
		$query->leftJoin ( DB::raw ( 'matter_actor_lnk lclic
			JOIN actor clic ON clic.id = lclic.actor_id' ), function ($join) {
			$join->on ( 'matter.container_id', 'lclic.matter_id' )->where ( [ 
					[ 'lclic.role', 'CLI' ],
					[ 'lclic.shared', 1 ] 
			] );
		} );
		
		if (array_key_exists ( 'Inventor1', $multi_filter )) {
			$query->leftJoin ( DB::raw ( 'matter_actor_lnk invlnk
				JOIN actor inv ON inv.id = invlnk.actor_id' ), function ($join) {
				$join->on ( DB::raw ( 'ifnull(matter.container_id, matter.id)' ), 'invlnk.matter_id' )->where ( 'invlnk.role', 'INV' );
			} );
		} else {
			$query->leftJoin ( DB::raw ( 'matter_actor_lnk invlnk
				JOIN actor inv ON inv.id = invlnk.actor_id' ), function ($join) {
				$join->on ( DB::raw ( 'ifnull(matter.container_id, matter.id)' ), 'invlnk.matter_id' )->where ( [ 
						[ 'invlnk.role', 'INV' ],
						[ 'invlnk.display_order', 1 ] 
				] );
			} );
		}
		
		$query->leftJoin ( DB::raw ( 'matter_actor_lnk agtlnk
			JOIN actor agt ON agt.id = agtlnk.actor_id' ), function ($join) {
			$join->on ( 'matter.id', 'agtlnk.matter_id' )->where ( [ 
					[ 'agtlnk.role', 'AGT' ],
					[ 'agtlnk.display_order', 1 ] 
			] );
		} );
		$query->leftJoin ( DB::raw ( 'matter_actor_lnk applnk
			JOIN actor app ON app.id = applnk.actor_id' ), function ($join) {
			$join->on ( 'matter.id', 'applnk.matter_id' )->where ( [ 
					[ 'applnk.role', 'APP' ],
					[ 'applnk.display_order', 1 ] 
			] );
		} );
		$query->leftJoin ( DB::raw ( 'matter_actor_lnk dellnk
			JOIN actor del ON del.id = dellnk.actor_id' ), function ($join) {
			$join->on ( DB::raw ( 'ifnull(matter.container_id,matter.id)' ), 'dellnk.matter_id' )->where ( 'dellnk.role', 'DEL' );
		} );
		$query->leftJoin ( 'event AS fil', function ($join) {
			$join->on ( 'matter.id', 'fil.matter_id' )->where ( 'fil.code', 'FIL' );
		} );
		$query->leftJoin ( 'event AS pub', function ($join) {
			$join->on ( 'matter.id', 'pub.matter_id' )->where ( 'pub.code', 'PUB' );
		} );
		$query->leftJoin ( 'event AS grt', function ($join) {
			$join->on ( 'matter.id', 'grt.matter_id' )->where ( 'grt.code', 'GRT' );
		} );
		$query->leftJoin ( DB::raw ( 'event status
			JOIN event_name ON event_name.code = status.code AND event_name.status_event = 1' ), 'matter.id', 'status.matter_id' );
		$query->leftJoin ( DB::raw ( 'event e2
			JOIN event_name en2 ON e2.code=en2.code AND en2.status_event = 1' ), function ($join) {
			$join->on ( 'status.matter_id', 'e2.matter_id' )->whereColumn ( 'status.event_date', '<', 'e2.event_date' );
		} );
		$query->leftJoin ( DB::raw ( 'classifier
			JOIN classifier_type ON classifier.type_code = classifier_type.code AND classifier_type.main_display = 1 AND classifier_type.display_order = 1' ), DB::raw ( 'IFNULL(matter.container_id, matter.id)' ), 'classifier.matter_id' );
		$query->where ( 'e2.matter_id', NULL );
		
		$role = Auth::user ()->default_role;
		$userid = Auth::user ()->id;
		
		if ($matter_category_display_type) {
			$query->where ( 'matter_category.display_with', $matter_category_display_type );
		}
		if ($role == 'CLI') {
			$query->whereRaw ( $userid . ' IN (cli.id, clic.id)' );
		}
		
		if (! empty ( $multi_filter )) {
			foreach ( $multi_filter as $key => $value ) {
				if ($value != '' && $key != 'display' && $key != 'display_style') {
					if ($key == 'responsible')
						$query->whereRaw ( "'$value' IN (matter.responsible, del.login)" );
					else
						$query->havingRaw ( "$key LIKE '$value%'" );
				}
			}
		}
		
		if ($sortField == 'caseref') {
			if ($sortDir == 'desc') {
				$query->orderByRaw ( 'matter.caseref DESC, matter.container_id, matter.origin, matter.country, matter.type_code, matter.idx' );
			} else {
				$query->orderByRaw ( 'matter.caseref, matter.container_id, matter.origin, matter.country, matter.type_code, matter.idx' );
			}
		} else {
			$query->orderByRaw ( "$sortField $sortDir, matter.caseref, matter.origin, matter.country" );
		}
		
		if ($paginated) {
			$matters = $query->simplePaginate ( 25 );
		} else {
			$matters = $query->get ();
		}

		return $matters;
	}

}
