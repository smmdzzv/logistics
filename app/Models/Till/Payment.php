<?php

namespace App\Models\Till;

use App\Models\BaseModel;
use App\Models\Currency;
use App\Models\Order;
use App\Models\Users\Cashier;
use App\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Relation;

/**
 * @property string branchId
 * @property string cashierId
 * @property string currencyId
 * @property string payerId
 * @property string paymentItemId
 * @property string accountToId
 * @property string exchangeId
 * @property double amount
 * @property Account accountTo
 * @property Account accountFrom
 */
class Payment extends BaseModel
{
    protected $casts = [
        'amount' => 'double'
    ];

    public function cashier()
    {
        return $this->belongsTo(Cashier::class, 'cashierId');
    }

    /**
     * Used for balance replenishment only, when accountFrom is null
     * @return BelongsTo
     */
    public function payer()
    {
        return $this->belongsTo(User::class, 'payerId');
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class, 'currencyId');
    }

    public function order(){
        return $this->hasOne(Order::class,'paymentId');
    }

    /**
     * Used for money transfer only, when accountFrom is null
     * @return BelongsTo
     */
    public function accountFrom()
    {
        return $this->belongsTo(Account::class, 'accountFromId');
    }

    public function accountTo()
    {
        return $this->belongsTo(Account::class, 'accountToId');
    }
}
