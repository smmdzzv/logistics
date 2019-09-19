<?php

namespace App\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Model;

class Position extends BaseModel
{
    protected $guarded = [];

    public function users(){
        return $this->hasMany(User::class);
    }
}
