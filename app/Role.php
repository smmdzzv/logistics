<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property string name
 * @property string description
 */
class Role extends Model
{
    public function users(){
        return $this->belongsToMany(User::class);
    }
}
