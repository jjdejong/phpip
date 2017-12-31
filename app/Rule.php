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
    
    protected function selectRule() {
          $select = DB::table('task_rules as r')
            ->leftJoin('country as c', 'r.for_country', '=', 'c.iso')                
			->leftJoin('country as o', 'r.for_origin', '=', 'o.iso')                
			->leftJoin('matter_category as mc', 'r.for_category', '=', 'mc.code')                
			->leftJoin('event_name as en', 'r.trigger_event', '=', 'en.code')                
			->leftJoin('matter_type as mt', 'r.for_type', '=', 'mt.code')                
			->leftJoin('event_name as tn', 'r.task', '=', 'tn.code')                
			->select( 'r.id as rule_id',
                      'r.task as task',
                      'r.for_category as category',
                      'r.for_country as country',
                      'r.for_origin as origin',
                      'r.for_type as f_type',
                      'r.detail as detail',
                      'c.name as country_name',
                      'o.name as origin_name',
                      'mc.category as category_name',
                      'en.name as trigger_event_name',
                      'mt.type as for_type_name',
                      'tn.name as task_name'
                      );
            
          return $select;
        }

        public function getRuleInfo($rule_id = 0) {
                if (! $rule_id)
                        return null;
          $select = DB::table('task_rules as r')
            ->leftJoin('country as c', 'r.for_country', '=', 'c.iso')
            ->leftJoin('country as o','o.iso' ,'=', 'r.for_origin')
			->leftJoin('matter_category as mc', 'r.for_category', '=', 'mc.code')                
			->leftJoin('event_name as en', 'r.trigger_event', '=', 'en.code')                
			->leftJoin('matter_type as mt', 'r.for_type', '=', 'mt.code')                
			->leftJoin('event_name as tn', 'r.task', '=', 'tn.code')                
            ->leftJoin('event_name as cn', 'cn.code','=', 'r.condition_event')
            ->leftJoin('event_name as an','an.code', '=', 'r.abort_on')
            ->leftJoin('actor as a', 'a.login', '=', 'r.responsible')
            ->select(           'c.name as country_name',
                                'o.name as origin_name',
                                'a.name as responsible_name',
                                'mt.type as for_type_name', 
                                'mc.category as category_name', 
                                'en.name as trigger_event_name',
                                'tn.name as task_name', 
                                'cn.name as condition_event_name', 
                                'an.name as abort_on_name', 
                                'r.id as rule_id' ,
                                'r.task as task' ,
                                'r.active as active',
                                'r.for_category as for_category' ,
                                'r.for_country as for_country',
                                'r.for_origin as for_origin',
                                'r.for_type as for_type',
                                'r.detail as detail',
                                'r.clear_task as clear_task' ,
                                'r.delete_task as delete_task' ,
                                'r.use_parent as use_parent',
                                'r.use_before as use_before',
                                'r.use_after as use_after',
                                'r.use_priority as use_priority',
                                'r.notes as notes',
                                'r.days as days'  ,
                                'r.months as months'  ,
                                'r.years as years'  ,
                                'r.recurring as recurring'  ,
                                'r.end_of_month as end_of_month'  ,
                                'r.abort_on as abort_on'  ,
                                'r.condition_event as condition_event'  ,
                                'r.cost as cost' ,
                                'r.fee as fee' ,
                                'r.currency as currency'  ,
                                'r.responsible as responsible')
                ->where ( 'r.ID','=', [$rule_id] );
                $result = $select->first();
                //echo dd($result);
                return $result;
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
