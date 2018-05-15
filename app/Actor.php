<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Actor extends Model
{
  protected $table = 'actor';
  public $timestamps = false;
  protected $hidden = ['login', 'last_login', 'password', 'remember_token', 'creator', 'updated', 'updater'];
  protected $guarded = ['id', 'password', 'creator', 'updated', 'updater'];

  use \Venturecraft\Revisionable\RevisionableTrait;
  protected $revisionEnabled = true;
  protected $revisionCreationsEnabled = true;
  protected $revisionCleanup = true; //Remove old revisions (works only when used with $historyLimit)
  protected $historyLimit = 500; //Maintain a maximum of 500 changes at any point of time, while cleaning up old revisions.

  public function company() {
  	return $this->belongsTo('App\Actor', 'company_id');
  }

  public function parent() {
  	return $this->belongsTo('App\Actor', 'parent_id');
  }

  public function matters() {
		return $this->hasMany('App\ActorPivot');
	}

  public function actorsList($Name=null, $Phy_person=null)
    {
                $select = $this->selectActor();
                 if ($Phy_person != '')
                        $select = $select->where ( 'a.phy_person','like', $Phy_person. '%');               
                if ($Name != '')
                        $select = $select->where ( 'a.name','like', $Name. '%');
                $select = $select->orderBy ( 'a.name' );
                //dd($select->get());
                return $select->get();
    }

    protected function selectActor() {
          $select = DB::table('actor as a')
            ->leftJoin('country as c', 'a.country', '=', 'c.iso')
            ->leftJoin('country as cm', 'a.country_mailing', '=', 'cm.iso')
            ->leftJoin('country as cb', 'a.country_billing', '=', 'cb.iso')
            ->leftJoin('actor as co', 'a.company_id', '=', 'co.id')
            ->leftJoin('actor as p', 'a.parent_id', '=', 'p.id')
            ->leftJoin('actor as as', 'a.site_id', '=', 'as.id')
            ->leftJoin('actor_role as ar', 'a.default_role', '=', 'ar.code')
            ->leftJoin('country as na', 'a.nationality', '=', 'na.iso')
            ->select(           'a.id as id',
								'a.name as name',	
								'a.first_name',
								'a.display_name',
								'a.login',
								'a.function',
								'a.phy_person',
								'a.small_entity',
								'a.address',
								'a.address_mailing',
								'a.email',
								'a.phone',
								'a.legal_form',
								'a.registration_no',
								'a.warn',
								'a.VAT_number',
								'a.notes',
								'a.address_billing',
								'c.name as country_name',
                                'cm.name as country_mailing_name',
                                'cb.name as country_billing_name',
                                'co.name as company_name',
                                'p.name as parent_name',
                                'as.name as site_name',
                                'ar.name as drole_name',
                                'na.name as nationality_name'
                                );
            
          return $select;
        }

        public function getActorInfo($actor_id = 0) {
                if (! $actor_id)
                        return null;
				$select =  $this->selectActor()->where ( 'a.id','=', $actor_id );
                $result = $select->first();
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
