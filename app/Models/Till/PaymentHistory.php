<?php

namespace App\Models\Till;

use App\Models\BaseModel;
use App\Models\Currency;
use App\Models\Users\Cashier;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;

class PaymentHistory extends BaseModel
{
    protected static function boot()
    {
        parent::boot();

        Relation::morphMap([
            'users' => 'App\User'
        ]);
    }

    public function cashier(){
        return $this->belongsTo(Cashier::class, 'cashierId');
    }

    public function currency(){
        return $this->belongsTo(Currency::class, 'currencyId');
    }

    public function accountFrom(){
        return $this->morphTo();
    }

    public function accountTo(){
        return $this->morphTo();
    }
}
