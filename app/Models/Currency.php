<?php

namespace App\Models;

use App\Models\Till\Account;
use App\Models\Till\MoneyExchange;

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

    public function exchangesTo(){
        return $this->hasMany(MoneyExchange::class, 'to');
    }

    public function exchangesFrom(){
        return $this->hasMany(MoneyExchange::class, 'from');
    }
}
