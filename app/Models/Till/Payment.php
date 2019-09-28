<?php

namespace App\Models\Till;

use App\Models\BaseModel;
use App\Models\Currency;
use App\Models\Users\Cashier;
use App\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Relation;

class Payment extends BaseModel
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

    /**
     * Used for balance replenishment only, when accountFrom is null
     * @return BelongsTo
     */
    public function payer(){
        return $this->belongsTo(User::class, 'payerId');
    }

    public function currency(){
        return $this->belongsTo(Currency::class, 'currencyId');
    }

    /**
     * Used for money transfer only, when accountFrom is null
     * @return BelongsTo
     */
    public function accountFrom(){
        return $this->morphTo();
    }

    public function accountTo(){
        return $this->morphTo();
    }
}
