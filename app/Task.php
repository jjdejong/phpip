<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $table = 'task';
    public $timestamps = false;
    protected $hidden = ['creator', 'updated', 'updater'];
    protected $guarded = ['id', 'creator', 'updated', 'updater'];

    use \Venturecraft\Revisionable\RevisionableTrait;

    protected $revisionEnabled = true;
    protected $revisionCreationsEnabled = true;
    protected $revisionCleanup = true; //Remove old revisions (works only when used with $historyLimit)
    protected $historyLimit = 500; //Maintain a maximum of 500 changes at any point of time, while cleaning up old revisions.

    public function info() {
        return $this->belongsTo('App\EventName', 'code');
    }

    public function trigger() {
        return $this->belongsTo('App\Event', 'trigger_id');
    }
    
    public function activeTasks() {
        return $this->with('trigger:id,matter_id','trigger.matter','info:code,name')
        ->join('event as trigger', 'trigger.id', '=', 'task.trigger_id')
        ->join('matter','matter.id','=','trigger.matter_id')
        ->join('event_name', 'event_name.code', '=', 'task.code')
        ->where('matter.dead', '0')
        ->where('task.done','0')
        ->orderby('due_date');
        
/*        return $this->with(['info:code,name', 'trigger:id,matter_id', 'trigger.matter:id,caseref,suffix','trigger.matter' => function ($query) {
            $query->where('dead', '0');
            }])
            ->where('done','0')
            ->orderby('due_date');     */   
    }

}
