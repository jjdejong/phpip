<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RenewalsLog extends Model
{
  protected $guarded = [];
  /**
   * Get the matter of the log line.
   */
  public function matter()
  {
      return $this->belongsTo('App\Matter');
  }

  /**
   * Get the matter of the creator.
   */
  public function creatorInfo()
  {
      return $this->belongsTo('App\User', 'creator','login');
  }

  /**
   * Get the matter of the renewal task.
   */
  public function task()
  {
      return $this->belongsTo('App\Task');
  }
}
