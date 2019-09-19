<?php

namespace App\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tariff extends BaseModel
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
