<?php

namespace App\Models;

use App\User;

/**
 * @property string name
 * @property string description
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


    public function users(){
        return $this->belongsToMany(User::class)
            ->using('App\Models\Pivots\BasePivot')
            ->withTimestamps();
    }
}
