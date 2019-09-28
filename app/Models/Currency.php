<?php

namespace App\Models;

use App\Models\Till\Account;

/**
 * @property string shortName
 * @property string name
 * @property mixed isoName
 */
class Currency extends BaseModel
{
    protected $guarded = [];

    public function accounts(){
        return $this->hasMany(Account::class);
    }
}
