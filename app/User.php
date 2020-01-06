<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
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

    // Password mutator for hashing the cleartext password as it is stored
    public function setPasswordAttribute($value) {
        $this->attributes['password'] = Hash::make($value);
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
