<?php

namespace App\Models\Till;

use App\Models\BaseModel;
use App\Models\Currency;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;

class Account extends BaseModel
{
    protected $casts = [
        'balance' => 'double'
    ];

    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        Relation::morphMap([
            'users' => 'App\User',
            'legalEntities' => 'App\Models\LegalEntities\LegalEntity'
        ]);
    }

    public function owner(){
        return $this->morphTo();
    }

    public function currency(){
        return $this->belongsTo(Currency::class, 'currencyId');
    }

    public function paymentsIn(){
        return $this->hasMany(Payment::class, 'accountToId');
    }

    public function paymentsOut(){
        return $this->hasMany(Payment::class, 'accountFromId');
    }
}