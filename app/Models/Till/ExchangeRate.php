<?php

namespace App\Models\Till;

use App\Models\BaseModel;
use App\Models\Currency;

class ExchangeRate extends BaseModel
{
    protected $guarded = [];

    public function fromCurrency(){
        return $this->belongsTo(Currency::class);
    }

    public function toCurrency(){
        return $this->belongsTo(Currency::class);
    }
}
