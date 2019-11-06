<?php

namespace App\Models\Users;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TrustedUser extends Model
{
    use SoftDeletes;

    protected $guarded = [];
}
