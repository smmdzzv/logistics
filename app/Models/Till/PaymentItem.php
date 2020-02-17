<?php

namespace App\Models\Till;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string description
 */
class PaymentItem extends BaseModel
{
    protected $guarded = [];

    public function scopePublic($query){
        return $query->where('title', '!=', 'Списание с баланса');
    }

    public function scopeBalanceOperations($query){
        return $query->where('title', 'Списание с баланса')->orWhere('title', 'Пополнение баланса');
    }
}
