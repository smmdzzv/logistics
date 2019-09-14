<?php

namespace App;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Model;

class Account extends BaseModel
{
    public function currency(){
        return $this->belongsTo(Currency::class);
    }

    public function owner(){
        return $this->belongsTo(User::class);
    }
}
