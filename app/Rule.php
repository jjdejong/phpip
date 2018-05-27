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
    //
    public function rulesList($Task=null, $Trigger=null, $Country=null)
    {
                $select = $this->selectRule();
                if ($Task != '')
                        $select = $select->where ( 'tn.name','like', $Task . '%');
                if ($Trigger != '')
                        $select = $select->where ( 'en.name','like', $Trigger . '%');
                if ($Country != '')
                        $select = $select->where ( 'c.name','like', $Country . '%');
                $select = $select->orderBy ( 'tn.name' );
                return $select->get();
    }
    
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
                // To fix: table_schema is hardcoded, it is to retreive
                $select =  DB::select("select column_name, column_comment from information_schema.columns WHERE `TABLE_SCHEMA` = 'phpipv2'  AND `TABLE_NAME` = ?",[$table_name])	;
                //$result = $select->get();
                $comments = array ();
                foreach ( $select as $column ) {
                        $col_name = $column->column_name;
                        $comments["$col_name"] = $column->column_comment;
                }
                return $comments;
        }
        
}
