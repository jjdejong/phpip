<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Rule extends Model
{
    protected $table = 'task_rules';
	public $timestamps = false; // removes timestamp updating in this table (done via MySQL triggers)
	protected $hidden = ['creator', 'updated', 'updater'];
	protected $guarded = ['id', 'creator', 'updated', 'updater'];
    
    public function country() {
		return $this->belongsTo('App\Country', 'for_country','iso');
	}

    public function origin() {
		return $this->belongsTo('App\Country', 'for_origin','iso');
	}

    public function category() {
		return $this->belongsTo('App\Category', 'for_category','code');
	}

    public function trigger() {
		return $this->belongsTo('App\EventName', 'trigger_event');
	}

    public function taskInfo() {
		return $this->belongsTo('App\EventName', 'task');
	}

    public function type() {
		return $this->belongsTo('App\Type', 'for_type','code');
	}

    public function condition_eventInfo() {
		return $this->belongsTo('App\EventName', 'condition_event');
	}

    public function abort_onInfo() {
		return $this->belongsTo('App\EventName', 'abort_on');
	}

    public function responsibleInfo() {
		return $this->belongsTo('App\Actor', 'responsible','login');
	}

        public function getTableComments($table_name = null) {
                if (! isset ( $table_name )) {
                        return false;
                }
                $tableInfo = DB::connection()->getDoctrineSchemaManager()->listTableDetails($table_name);
                $comments = array ();
                foreach ($tableInfo->getColumns() as $column) {
                    $comments[$column->getName()] = $column->getComment();
                }
                return $comments;
        }
        
}
