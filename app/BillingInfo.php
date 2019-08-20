<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BillingInfo extends Model
{
    public function tariffPrice(){
        return $this->belongsTo(TariffPriceHistory::class);
    }
}
