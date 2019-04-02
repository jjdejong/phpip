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

  //use \Venturecraft\Revisionable\RevisionableTrait;
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

  public function site() {
  	return $this->belongsTo('App\Actor', 'site_id');
  }

  public function matters() {
		return $this->hasMany('App\ActorPivot');
	}

  public function droleInfo() {
  	return $this->belongsTo('App\Role', 'default_role');
  }

  public function countryInfo() {
  	return $this->belongsTo('App\Country', 'country');
  }

  public function country_mailingInfo() {
  	return $this->belongsTo('App\Country', 'country_mailing');
  }

  public function country_billingInfo() {
  	return $this->belongsTo('App\Country', 'country_billing');
  }

  public function nationalityInfo() {
  	return $this->belongsTo('App\Country', 'nationality');
  }

  public function getTableComments($table_name = null) {
    if (! isset ( $table_name )) {
      return false;
    }
    // To fix: table_schema is hardcoded, it is to retreive
    $tableInfo = DB::connection()->getDoctrineSchemaManager()->listTableDetails($table_name);
    //$select =  DB::select("select column_name, column_comment from information_schema.columns WHERE `TABLE_SCHEMA` = 'phpipv2'  AND `TABLE_NAME` = ?",[$table_name])	;
    //$result = $select->get();
    $comments = [];
    foreach ( $tableInfo->getColumns() as $column ) {
      $col_name = $column->getName();
      $comments[$col_name] = $column->getComment();
    }
    return $comments;
  }

}
