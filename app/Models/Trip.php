<?php

namespace App\Models;

use App\Models\StoredItems\StoredItem;
use App\Models\Users\Driver;
use Illuminate\Database\Eloquent\Collection;

/**
 * @property string departureDate
 * @property string departureAt
 * @property string returnedAt
 * @property string status
 * @property bool hasTrailer
 * @property Car car
 * @property Collection<StoredItem> storedItems
 * @property FuelConsumption toConsumption
 * @property FuelConsumption fromConsumption
 * @property boolean emptyToDestination
 * @property mixed emptyFromDestination
 * @property int routeLengthToDestination
 * @property int routeLengthFromDestination
 * @property mixed routeLengthWithCargoFrom
 * @property mixed cargoWeightFrom
 * @property mixed trailerCargoWeightFrom
 * @property mixed routeLengthWithCargoTo
 * @property mixed cargoWeightTo
 * @property mixed trailerCargoWeightTo
 * @property array|string|null totalFuelConsumption
 * @property integer mileageAfter
 * @property string id
 * @property Branch departureBranch
 * @property Branch destinationBranch
 */
class Trip extends BaseModel
{
    public const STATUS_ACTIVE = 'active';

    public const STATUS_COMPLETED = 'completed';

    public const STATUS_CANCELED = 'canceled';

    public const STATUS_SCHEDULED = 'scheduled';

    protected $casts = [
        'hasTrailer' => 'boolean',
        'emptyToDestination' => 'boolean',
        'emptyFromDestination' => 'boolean',
        'mileageAfter' => 'integer'
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
            ->as('tripHistory')
            ->withPivot('status')
            ->withTimestamps();
    }

    public function loadedItems()
    {
        return $this->storedItems()->doesntHave('storageHistory');
    }

    public function unloadedItems()
    {
        return $this->storedItems()->has('storageHistory');
    }

    public function isEditable()
    {
//        $current = Carbon::now()->toDateString();
//        return $this->departureDate >= $current && $this->status === 'created';
        return $this->status === self::STATUS_SCHEDULED;
    }

    public function destinationBranch()
    {
        return $this->belongsTo(Branch::class, 'destination_branch_id');
    }

    public function departureBranch()
    {
        return $this->belongsTo(Branch::class, 'departure_branch_id');
    }

    public function toConsumption()
    {
        return $this->belongsTo(FuelConsumption::class);
    }

    public function fromConsumption()
    {
        return $this->belongsTo(FuelConsumption::class);
    }

    public function getCalculatedConsumptionTo()
    {
        return $this->calculateConsumption(
            $this->toConsumption,
            $this->routeLengthToDestination,
            $this->routeLengthWithCargoTo,
            $this->cargoWeightTo,
            $this->trailerCargoWeightTo
        );
    }

    public function getCalculatedConsumptionFrom()
    {
        return $this->calculateConsumption(
            $this->toConsumption,
            $this->routeLengthFromDestination,
            $this->routeLengthWithCargoFrom,
            $this->cargoWeightFrom,
            $this->trailerCargoWeightFrom
        );
    }

    /**
     * Return calculated fuel consumption
     * @param FuelConsumption $fuelConsumption
     * @param $fullDistance
     * @param $loadedDistance
     * @param $carItemsWeight
     * @param $trailerItemsWeight
     * @return float
     */
    public function calculateConsumption(FuelConsumption $fuelConsumption, $fullDistance,
                                         $loadedDistance, $carItemsWeight, $trailerItemsWeight)
    {
        $consumption = $fuelConsumption->forEmpty * ($fullDistance - $loadedDistance)
            + $fuelConsumption->forLoaded * $carItemsWeight / 1000 * $loadedDistance;

        if ($this->hasTrailer)
            $consumption += $fuelConsumption->forEmptyTrailer * ($fullDistance - $loadedDistance)
                + $fuelConsumption->forLoadedTrailer * $trailerItemsWeight / 1000 * $loadedDistance;

        return round($consumption, 2);
    }
}
