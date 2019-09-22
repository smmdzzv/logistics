<?php

namespace App\Models;

use App\Models\Users\Driver;
use App\User;

/**
 * @property string name
 * @property string description
 * @property string title
 */
class Role extends BaseModel
{
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime'
    ];


    public function users()
    {
        return $this->belongsToMany(User::class)
            ->using('App\Models\Pivots\BasePivot')
            ->withTimestamps();
    }

    public function concreteUsers($class){
        return $this->belongsToMany($class, 'role_user',  'role_id', 'user_id')
            ->using('App\Models\Pivots\BasePivot')
            ->withTimestamps();
    }
}
