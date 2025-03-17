<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $guarded = ['id', 'created_at', 'updated_at'];
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'login', 'password', 'email', 'language', 'company_id', 
        'default_role', 'phone', 'notes', 'updater'
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'language' => 'string',
    ];

    /**
     * Get the user's preferred language.
     * 
     * This can include region information (e.g., en_GB, en_US) for date formatting.
     * The language part (e.g., 'en' from 'en_GB') is used for UI translations.
     * 
     * @param bool $forTranslation Whether to return the base language for translations
     * @return string
     */
    public function getLanguage($forTranslation = false)
    {
        $language = $this->language ?? config('app.locale');
        
        // For translations, English variants (en_GB, en_US) should all use 'en'
        if ($forTranslation) {
            $baseLanguage = explode('_', $language)[0];
            
            if ($baseLanguage === 'en') {
                return 'en';
            }
        }
        
        return $language;
    }

    public function roleInfo()
    {
        return $this->belongsTo(Role::class, 'default_role');
    }

    public function company()
    {
        return $this->belongsTo(Actor::class, 'company_id');
    }

    public function parent()
    {
        return $this->belongsTo(Actor::class, 'parent_id');
    }

    public function matters()
    {
        return $this->hasMany(Matter::class, 'responsible', 'login');
    }

    public function tasks()
    {
        return $this->matters()->has('tasksPending')->with('tasksPending');
    }

    public function renewals()
    {
        return $this->matters()->has('renewalsPending')->with('renewalsPending');
    }
}
