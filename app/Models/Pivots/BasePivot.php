<?php

namespace App\Models\Pivots;

use App\Traits\UsesUUID;
use Illuminate\Database\Eloquent\Relations\Pivot;

class BasePivot extends Pivot
{
    use UsesUUID;

    public $incrementing = false;
    protected $primaryKey = 'id';
    protected $keyType = 'uuid';
}
