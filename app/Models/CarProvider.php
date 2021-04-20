<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property string name
 */
class CarProvider extends BaseModel
{
    use HasFactory;

    use SoftDeletes;
}
