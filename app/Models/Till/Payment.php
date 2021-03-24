<?php

namespace App\Models\Till;

use App\Models\BaseModel;
use App\Models\Branch;
use App\Models\Currency;
use App\Models\StoredItems\ItemsSelection;
use App\Models\Users\Client;
use App\Services\Client\ClientExpensesReportService;
use App\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * @property string branch_id
 * @property string cashier_id
 * @property string prepared_by_id
 * @property string status
 * @property string payer_id
 * @property string payer_account_in_bill_currency_id
 * @property string payer_account_in_second_currency_id
 * @property string payee_id
 * @property string payee_account_in_bill_currency_id
 * @property string payee_account_in_second_currency_id
 * @property string payment_item_id
 * @property double billAmount
 * @property double paidAmountInBillCurrency
 * @property mixed paidAmountInSecondCurrency
 * @property string bill_currency_id
 * @property string paid_currency_id
 * @property string exchange_rate_id
 * @property string comment
 * @property User|Branch payer
 * @property User|Branch payee
 * @property Account payerAccountInBillCurrency
 * @property Account payeeAccountInBillCurrency
 * @property string payer_type
 * @property string payee_type
 * @property float billAmountInTjs
 * @property string exchange_rate_to_tjs
 * @property integer placesLeft
 * @property double clientDebt
 * @property integer number
 * @property float second_paid_currency_id
 * @property PaymentItem paymentItem
 * @property Currency billCurrency
 * @property Currency|null secondPaidCurrency
 * @property Collection relatedPayments
 * @property string id
 * @property string client_items_selection_id
 */
class Payment extends BaseModel
{
    use SoftDeletes;

    public const STATUS_COMPLETED = 'completed';

    public const STATUS_PENDING = 'pending';

    protected $casts = [
        'billAmount' => 'double',
        'paidAmountInBillCurrency' => 'double',
        'paidAmountInSecondCurrency' => 'double',
        'approved' => 'bool'
    ];

    protected $guarded = [];

    /**
     * The relationships that should always be loaded.
     *
     * @var array
     */
    protected $with = [
        'branch',
        'editor',
        'creator',
        'destroyer',
        'payer',
        'payee',
        'payerAccountInBillCurrency',
        'payerAccountInSecondCurrency',
        'payeeAccountInBillCurrency',
        'payeeAccountInSecondCurrency',
        'paymentItem',
        'billCurrency',
        'secondPaidCurrency',
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


    public function payerAccountInBillCurrency()
    {
        return $this->belongsTo(Account::class);
    }

    public function payerAccountInSecondCurrency()
    {
        return $this->belongsTo(Account::class);
    }

    public function payeeAccountInBillCurrency()
    {
        return $this->belongsTo(Account::class);
    }

    public function payeeAccountInSecondCurrency()
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

    public function secondPaidCurrency()
    {
        return $this->belongsTo(Currency::class);
    }

    public function paidCurrency()
    {
        return $this->belongsTo(Currency::class);
    }

    public function exchangeRate()
    {
        return $this->belongsTo(ExchangeRate::class);
    }

    public function relatedPayment()
    {
        return $this->belongsTo(Payment::class);
    }

    public function relatedPayments()
    {
        return $this->hasMany(Payment::class, 'related_payment_id');
    }

//    public function orderPayments()
//    {
//        return $this->hasMany(Order\StoredItemsSelection::class);
//    }
//
//    public function orderPayment()
//    {
//        return $this->hasOne(Order\StoredItemsSelection::class);
//    }

//    public function orderPaymentItems()
//    {
//        return $this->hasManyThrough(StoredItem::class, ItemsSelection::class);
//    }

    public function clientItemsSelection()
    {
        return $this->belongsTo(ItemsSelection::class, 'client_items_selection_id');
    }

    public function fillExtras()
    {
        $dateFrom = Carbon::now()->toDateString();
        $dateTo = Carbon::now()->addDay()->toDateString();
        $lastPayment = Payment::select('id', 'number', 'branch_id')
            ->where('id', '!=', $this->id)
            ->where('branch_id', $this->branch_id)
            ->where('created_at', '>=', $dateFrom)
            ->where('created_at', '<', $dateTo)
            ->latest()
            ->first();

        if (!$this->number)
            $this->number = $lastPayment ? $lastPayment->number + 1 : 1;
        $somoni = Currency::where('isoName', 'TJS')->firstOrFail();

        if ($this->billCurrency->id !== $somoni->id) {
            $exchangeRate = ExchangeRate::where('from_currency_id', $this->bill_currency_id)
                ->where('to_currency_id', $somoni->id)
                ->firstOrFail();

            $this->billAmountInTjs = round($this->billAmount * $exchangeRate->coefficient, 2);
            $this->exchange_rate_to_tjs = $exchangeRate->id;
        }

        if ($this->payer_type === 'user') {
            $client = Client::findOrFail($this->payer->id);

            $service = new ClientExpensesReportService();

            $stat = $service->generate($client, Carbon::now()->addDay(), null);

            $this->placesLeft = $stat->placesCountAtStart;
            $this->clientDebt = $stat->debtAtStart;
        }
    }
}
