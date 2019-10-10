<?php

namespace App\Models;

use App\Models\StoredItems\StoredItem;
use App\Models\StoredItems\StoredItemTripHistory;
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

    public function storedItems()
    {
        return $this->belongsToMany(
            StoredItem::class,
            'stored_item_trip_histories',
            'trip_id',
            'stored_item_id')
            ->using('App\Models\Pivots\BasePivot')
            ->withTimestamps();
    }
}
