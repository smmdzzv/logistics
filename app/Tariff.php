<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tariff extends Model
{
    use SoftDeletes;

    protected $guarded = [];

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
