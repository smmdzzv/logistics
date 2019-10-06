<?php

namespace App\Models;

use App\Models\StoredItems\StoredItem;
use App\Models\Users\Driver;

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

    public function storedItems(){
        return $this->hasMany(StoredItem::class, 'tripId');
    }
}
