<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tariff extends Model
{
    public function items(){
        return $this->hasMany(Item::class);
    }

    public function priceHistories(){
        return $this->hasMany(TariffPriceHistory::class);
    }

    public function lastPriceHistory(){
        return $this->hasMany(TariffPriceHistory::class)->first();
    }
}
