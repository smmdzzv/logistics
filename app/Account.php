<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    public function currency(){
        return $this->belongsTo(Currency::class);
    }

    public function user(){
        return $this->belongsTo(User::class);
    }
}
