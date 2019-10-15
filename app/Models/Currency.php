<?php

namespace App\Models;

use App\Models\Till\Account;
use App\Models\Till\MoneyExchange;

/**
 * @property string shortName
 * @property string name
 * @property string isoName
 * @property string country_id
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

    public function country(){
        return $this->belongsTo(Country::class);
    }
}
