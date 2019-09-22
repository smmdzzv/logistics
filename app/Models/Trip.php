<?php

namespace App\Models;

use App\Models\Users\Driver;
use Illuminate\Database\Eloquent\Model;
use Rorecek\Ulid\HasUlid;

class Trip extends BaseModel
{
    protected $fillable = ['code', 'driverId', 'carId', 'departureDate', 'returnDate'];

    public function driver()
    {
        return $this->belongsTo(Driver::class, 'driverId');
    }

    public function car()
    {
        return $this->belongsTo(Car::class, 'carId');
    }
}
