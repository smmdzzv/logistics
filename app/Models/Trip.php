<?php

namespace App\Models;

use App\Models\StoredItems\StoredItem;
use App\Models\StoredItems\StoredItemTripHistory;
use App\Models\Users\Driver;
use Carbon\Carbon;

/**
 * @property string departureDate
 */
class Trip extends BaseModel
{
    protected $guarded = [];

    protected $casts = [
        'hasTrailer' => 'boolean'
    ];

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
            ->wherePivot('deleted_at', null)
            ->withTimestamps();
    }

    public function loadedItems()
    {
        return $this->storedItems()->doesntHave('storageHistory');
    }

    public function isEditable()
    {
        return $this->departureDate > Carbon::now();
    }
}
