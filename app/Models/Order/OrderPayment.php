<?php

namespace App\Models\Order;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderPayment extends BaseModel
{
    use SoftDeletes;
}
