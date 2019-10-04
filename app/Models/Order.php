<?php

namespace App\Models;

use App\Models\BaseModel;
use App\Models\Till\Payment;
use App\Models\Users\Client;
use App\User;
use Illuminate\Database\Eloquent\Model;

/**
 * @property double totalCubage
 * @property double totalWeight
 * @property double totalPrice
 * @property double totalDiscount
 * @property double totalCount
 */
class Order extends BaseModel
{
    protected $casts = [
        'totalCubage' => 'double',
        'totalWeight' => 'double',
        'totalPrice' => 'double',
        'totalDiscount' => 'double',
        'totalCount' => 'double',
    ];

    public function storedItems()
    {
        return $this->hasMany(StoredItem::class);
    }

    public function owner()
    {
        return $this->belongsTo(Client::class, 'ownerId');
    }

    public function registeredBy()
    {
        return $this->belongsTo(User::class, 'registeredBy');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch');
    }

    public function payment(){
        return $this->belongsTo(Payment::class,'paymentId');
    }

    private function roundNumeric()
    {
        $this->totalCubage = round($this->totalCubage, 2);
        $this->totalWeight = round($this->totalWeight, 2);
        $this->totalPrice = round($this->totalPrice, 2);
        $this->totalDiscount = round($this->totalDiscount, 2);
        $this->totalCount = round($this->totalCount, 2);
    }
}
