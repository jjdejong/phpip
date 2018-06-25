<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Classifier extends Model {

    protected $table = 'classifier';
    public $timestamps = false;
    protected $hidden = ['creator', 'updated', 'updater'];
    protected $guarded = ['id', 'creator', 'updated', 'updater'];

    use \Venturecraft\Revisionable\RevisionableTrait;

    protected $revisionEnabled = true;
    protected $revisionCreationsEnabled = true;
    protected $revisionCleanup = true; //Remove old revisions (works only when used with $historyLimit)
    protected $historyLimit = 500; //Maintain a maximum of 500 changes at any point of time, while cleaning up old revisions.

    public function type() {
        return $this->belongsTo('App\ClassifierType', 'type_code');
    }

    public function linkedMatter() {
        return $this->belongsTo('App\Matter', 'lnk_matter_id');
    }

}
