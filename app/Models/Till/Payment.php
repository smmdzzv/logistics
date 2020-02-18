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
 * @property string branch_id
 * @property string cashier_id
 * @property string prepared_by_id
 * @property string status
 * @property string payer_id
 * @property string payer_account_id
 * @property string payerType
 * @property string payee_id
 * @property string payee_account_id
 * @property string payeeType
 * @property string payment_item_id
 * @property double billAmount
 * @property double paidAmount
 * @property string bill_currency_id
 * @property string paid_currency_id
 * @property string exchange_rate_id
 * @property string comment
 * @property Account payerAccount
 * @property Account payeeAccount
 * @property User|Branch payer
 * @property User|Branch payee
 */
class Payment extends BaseModel
{
    use SoftDeletes;

    protected $casts = [
        'billAmount' => 'double',
        'paidAmount' => 'double'
    ];

    protected $guarded = [];

    /**
     * The relationships that should always be loaded.
     *
     * @var array
     */
    protected $with = [
        'branch',
        'preparedBy',
        'cashier',
        'payer',
        'payee',
        'payerAccount',
        'payeeAccount',
        'paymentItem',
        'billCurrency',
        'paidCurrency',
        'exchangeRate'
    ];

    protected static function boot()
    {
        parent::boot();

        Relation::morphMap([
            'user' => 'App\User',
            'branch' => 'App\Models\Branch'
        ]);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function cashier()
    {
        return $this->belongsTo(User::class);
    }

    public function preparedBy()
    {
        return $this->belongsTo(User::class);
    }

    public function payer()
    {
        return $this->morphTo();
    }

    public function payee()
    {
        return $this->morphTo();
    }

    private function getSubject($type)
    {
        switch ($type) {
            case 'user':
                return $this->belongsTo(User::class);
            case 'branch':
                return $this->belongsTo(Branch::class);
        }
    }


    public function payerAccount()
    {
        return $this->belongsTo(Account::class);
    }

    public function payeeAccount()
    {
        return $this->belongsTo(Account::class);
    }

    public function paymentItem()
    {
        return $this->belongsTo(PaymentItem::class);
    }

    public function billCurrency()
    {
        return $this->belongsTo(Currency::class);
    }

    public function paidCurrency()
    {
        return $this->belongsTo(Currency::class);
    }

    public function exchangeRate()
    {
        return $this->belongsTo(MoneyExchange::class);
    }

    public function orderPayments()
    {
        return $this->hasMany(Order\OrderPayment::class);
    }

    public function orderPayment()
    {
        return $this->hasOne(Order\OrderPayment::class);
    }

    public function orderPaymentItems()
    {
        return $this->hasManyThrough(Order\OrderPaymentItem::class, OrderPayment::class);
    }
}
