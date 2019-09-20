<?php

namespace App\Models;

use App\Models\Users\Driver;
use Illuminate\Database\Eloquent\Model;
use Rorecek\Ulid\HasUlid;

class Trip extends Model
{
    use HasUlid;

    public function driver(){
        return $this->belongsTo(Driver::class, 'driverId');
    }
}
