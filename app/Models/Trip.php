<?php

namespace App\Models;

use App\Models\StoredItems\StoredItem;
use App\Models\StoredItems\StoredItemTripHistory;
use App\Models\Users\Driver;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

/**
 * @property string departureDate
 * @property string departureAt
 * @property string returnedAt
 * @property string status
 * @property bool hasTrailer
 * @property Car car
 * @property Collection<StoredItem> storedItems
 */
class Trip extends BaseModel
{
    protected $guarded = [];

    protected $casts = [
        'hasTrailer' => 'boolean',
        'emptyToDestination' => 'boolean',
        'emptyFromDestination' => 'boolean'
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
        $current = Carbon::now()->toDateString();
        return $this->departureDate >= $current && $this->status === 'created';
    }

    public function destinationBranch()
    {
        return $this->belongsTo(Branch::class, 'destination_branch_id');
    }

    public function departureBranch()
    {
        return $this->belongsTo(Branch::class, 'departure_branch_id');
    }
}
