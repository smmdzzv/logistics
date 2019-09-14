<?php

namespace App;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Model;

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
