<?php

namespace App\Models\Order;

use App\Models\BaseModel;
use App\Models\Order;
use App\Models\Till\Payment;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderPayment extends BaseModel
{
    use SoftDeletes;

    protected $guarded = [];

    public function order(){
        return $this->belongsTo(Order::class);
    }

    public function payment(){
        return $this->belongsTo(Payment::class);
    }

    public function paidItems(){
        return $this->hasMany(OrderPaymentItem::class);
    }
}
