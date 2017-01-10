<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class Matter extends Model {
	protected $table = 'matter';
	protected $primaryKey = 'ID'; // necessary because "id" is expected by default and we have "ID"
	public $timestamps = false; // removes timestamp updating in this table (done via MySQL triggers)
	
	public function list($sortField = 'caseref', $sortDir = 'asc', $multi_filter = [], $matter_category_display_type = false, $paginated = false) {
		$query = $this->select ( DB::raw ( "CONCAT_WS('', CONCAT_WS('-', CONCAT_WS('/', concat(caseref, matter.country), origin), matter.type_code), idx) AS Ref,
			matter.country AS country,
			matter.category_code AS Cat,
			matter.origin,
			event_name.name AS Status,
			status.event_date AS Status_date,
			COALESCE(cli.display_name, clic.display_name, cli.name, clic.name) AS Client,
			COALESCE(clilnk.actor_ref, lclic.actor_ref) AS ClRef,
			COALESCE(app.display_name, app.name) AS Applicant,
			COALESCE(agt.display_name, agt.name) AS Agent,
			agtlnk.actor_ref AS AgtRef,
			classifier.value AS Title,
			CONCAT_WS(' ', inv.name, inv.first_name) as Inventor1,
			fil.event_date AS Filed,
			fil.detail AS FilNo,
			pub.event_date AS Published,
			pub.detail AS PubNo,
			grt.event_date AS Granted,
			grt.detail AS GrtNo,
			matter.ID,
			matter.container_ID,
			matter.parent_ID,
			matter.responsible,
			del.login AS delegate,
			matter.dead,
			IF(isnull(matter.container_ID),1,0) AS Ctnr" ) );
		
		$query->join ( 'matter_category', 'matter.category_code', 'matter_category.code' );
		$query->leftJoin ( DB::raw ( 'matter_actor_lnk clilnk
			JOIN actor cli ON cli.ID = clilnk.actor_ID' ), function ($join) {
			$join->on ( 'matter.ID', 'clilnk.matter_ID' )->where ( 'clilnk.role', 'CLI' );
		} );
		$query->leftJoin ( DB::raw ( 'matter_actor_lnk lclic
			JOIN actor clic ON clic.ID = lclic.actor_ID' ), function ($join) {
			$join->on ( 'matter.container_ID', 'lclic.matter_ID' )->where ( [ 
					[ 'lclic.role', 'CLI' ],
					[ 'lclic.shared', 1 ] 
			] );
		} );
		
		if (array_key_exists ( 'Inventor1', $multi_filter )) {
			$query->leftJoin ( DB::raw ( 'matter_actor_lnk invlnk
				JOIN actor inv ON inv.ID = invlnk.actor_ID' ), function ($join) {
				$join->on ( DB::raw ( 'ifnull(matter.container_ID, matter.ID)' ), 'invlnk.matter_ID' )->where ( 'invlnk.role', 'INV' );
			} );
		} else {
			$query->leftJoin ( DB::raw ( 'matter_actor_lnk invlnk
				JOIN actor inv ON inv.ID = invlnk.actor_ID' ), function ($join) {
				$join->on ( DB::raw ( 'ifnull(matter.container_ID, matter.ID)' ), 'invlnk.matter_ID' )->where ( [ 
						[ 'invlnk.role', 'INV' ],
						[ 'invlnk.display_order', 1 ] 
				] );
			} );
		}
		
		$query->leftJoin ( DB::raw ( 'matter_actor_lnk agtlnk
			JOIN actor agt ON agt.ID = agtlnk.actor_ID' ), function ($join) {
			$join->on ( 'matter.ID', 'agtlnk.matter_ID' )->where ( [ 
					[ 'agtlnk.role', 'AGT' ],
					[ 'agtlnk.display_order', 1 ] 
			] );
		} );
		$query->leftJoin ( DB::raw ( 'matter_actor_lnk applnk
			JOIN actor app ON app.ID = applnk.actor_ID' ), function ($join) {
			$join->on ( 'matter.ID', 'applnk.matter_ID' )->where ( [ 
					[ 'applnk.role', 'APP' ],
					[ 'applnk.display_order', 1 ] 
			] );
		} );
		$query->leftJoin ( DB::raw ( 'matter_actor_lnk dellnk
			JOIN actor del ON del.ID = dellnk.actor_ID' ), function ($join) {
			$join->on ( DB::raw ( 'ifnull(matter.container_ID,matter.ID)' ), 'dellnk.matter_ID' )->where ( 'dellnk.role', 'DEL' );
		} );
		$query->leftJoin ( 'event AS fil', function ($join) {
			$join->on ( 'matter.ID', 'fil.matter_ID' )->where ( 'fil.code', 'FIL' );
		} );
		$query->leftJoin ( 'event AS pub', function ($join) {
			$join->on ( 'matter.ID', 'pub.matter_ID' )->where ( 'pub.code', 'PUB' );
		} );
		$query->leftJoin ( 'event AS grt', function ($join) {
			$join->on ( 'matter.ID', 'grt.matter_ID' )->where ( 'grt.code', 'GRT' );
		} );
		$query->leftJoin ( DB::raw ( 'event status
			JOIN event_name ON event_name.code = status.code AND event_name.status_event = 1' ), 'matter.ID', 'status.matter_ID' );
		$query->leftJoin ( DB::raw ( 'event e2
			JOIN event_name en2 ON e2.code=en2.code AND en2.status_event = 1' ), function ($join) {
			$join->on ( 'status.matter_id', 'e2.matter_id' )->whereColumn ( 'status.event_date', '<', 'e2.event_date' );
		} );
		$query->leftJoin ( DB::raw ( 'classifier
			JOIN classifier_type ON classifier.type_code = classifier_type.code AND classifier_type.main_display = 1 AND classifier_type.display_order = 1' ), DB::raw ( 'IFNULL(matter.container_ID, matter.ID)' ), 'classifier.matter_ID' );
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
		
		/*
		\Event::listen('Illuminate\Database\Events\QueryExecuted', function($query) {
			var_dump($query->sql);
		 	var_dump($query->bindings);
		});
		*/
		
		if ($paginated) {
			$matters = $query->simplePaginate ( 25 );
		} else {
			$matters = $query->get ();
		}

		return $matters;
	}
}
