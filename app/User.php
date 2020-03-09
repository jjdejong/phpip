<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    protected $guarded = ['id', 'created_at', 'updated_at'];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function roleInfo()
    {
        return $this->belongsTo('App\Role', 'default_role');
    }

    public function company()
    {
        return $this->belongsTo('App\Actor', 'company_id');
    }

    public function parent()
    {
        return $this->belongsTo('App\Actor', 'parent_id');
    }

    public function matters() {
        return $this->hasMany('App\Matter', 'responsible', 'login');
    }

    public function tasks() {
        return $this->matters()->has('tasksPending')->with('tasksPending');
    }

    public function renewals() {
        return $this->matters()->has('renewalsPending')->with('renewalsPending');
    }
}
