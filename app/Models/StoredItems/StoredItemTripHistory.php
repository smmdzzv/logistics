<?php

namespace App\Models\StoredItems;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class StoredItemTripHistory extends BaseModel
{
    protected $guarded = [];

    use SoftDeletes;
}
