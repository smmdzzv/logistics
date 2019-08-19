<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property string shortName
 * @property string name
 */
class Currency extends Model
{
    public function accounts(){
        return $this->hasMany(Account::class);
    }
}
