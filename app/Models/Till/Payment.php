<?php

namespace App\Models\Till;

use App\Models\BaseModel;
use App\Models\Branch;
use App\Models\Currency;
use App\Models\Order;
use App\Models\Order\OrderPayment;
use App\Models\StoredItems\StoredItem;
use App\Models\Users\Cashier;
use App\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Eloquent\SoftDeletes;

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
 * @property string comment
 */
class Payment extends BaseModel
{
    use SoftDeletes;

    protected $casts = [
        'amount' => 'double'
    ];

    protected $guarded = [];

    public function cashier()
    {
        return $this->belongsTo(User::class, 'cashierId');
    }

    public function preparedBy()
    {
        return $this->belongsTo(User::class, 'preparedById');
    }

    /**
     * Used for balance replenishment only, when accountFrom is null
     * @return BelongsTo
     */
    public function payer()
    {
        return $this->belongsTo(User::class, 'payerId');
    }

    /**
     * Used for outgoing payments
     * @return BelongsTo
     */
    public function recipient()
    {
        return $this->belongsTo(User::class, 'recipient_id');
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class, 'currencyId');
    }

//    public function order(){
//        return $this->hasOne(Order::class,'paymentId');
//    }

    public function paymentItem(){
        return $this->belongsTo(PaymentItem::class, 'paymentItemId');
    }

    public function branch(){
        return $this->belongsTo(Branch::class, 'branchId');
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

    public function exchange(){
        return $this->belongsTo(MoneyExchange::class, 'exchangeId');
    }

    public function orderPayments(){
        return $this->hasMany(Order\OrderPayment::class);
    }

    public function orderPayment(){
        return  $this->hasOne(Order\OrderPayment::class);
    }

    public function orderPaymentItems(){
        return $this->hasManyThrough(Order\OrderPaymentItem::class, OrderPayment::class);
    }
}
