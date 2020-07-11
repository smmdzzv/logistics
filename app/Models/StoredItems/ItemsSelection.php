<?php

namespace App\Models\StoredItems;

use App\Models\BaseModel;
use App\Models\Order;
use App\Models\Till\Payment;
use App\User;
use Illuminate\Database\Eloquent\SoftDeletes;


class ItemsSelection extends BaseModel
{
    use SoftDeletes;

    protected $guarded = [];

    public function storedItems()
    {
        return $this->belongsToMany(StoredItem::class)
            ->using('App\Models\Pivots\BasePivot');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function user(){
        return $this->belongsTo(User::class);
    }
}
