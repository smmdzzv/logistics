<?php

namespace App\Models\StoredItems;

use App\Models\BaseModel;
use App\Models\Order;
use App\Models\Till\Payment;
use Illuminate\Database\Eloquent\SoftDeletes;


class ClientItemsSelection extends BaseModel
{
    use SoftDeletes;

    protected $guarded = [];

    public function storedItems()
    {
        return $this->belongsToMany(StoredItem::class)
            ->using('App\Models\Pivots\BasePivot');
    }

//    public function order(){
//        return $this->belongsTo(Order::class);
//    }
//
//    public function payment(){
//        return $this->belongsTo(Payment::class);
//    }
//
//    public function paidItems(){
//        return $this->hasMany(OrderPaymentItem::class);
//    }
}
