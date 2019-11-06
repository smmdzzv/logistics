<?php

namespace App\Models\Users;

use App\Models\BaseModel;
use App\User;
use Illuminate\Database\Eloquent\SoftDeletes;

class TrustedUser extends BaseModel
{
    use SoftDeletes;

    protected $guarded = [];

    public function user(){
        return $this->belongsTo(User::class);
    }
}
