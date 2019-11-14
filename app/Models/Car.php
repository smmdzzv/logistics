<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;

/**
 * @property double maxCubage
 * @property double maxWeight
 * @property double trailerMaxCubage
 * @property double trailerMaxWeight
 * @property string trailerNumber
 * @property double width
 * @property double height
 * @property double length
 * @property string number
 */
class Car extends BaseModel
{
    protected $guarded = [];

    protected $casts = [
        'maxWeight' => 'double',
        'maxCubage' => 'double',
        'length' => 'double',
        'height' => 'double',
        'width' => 'double',
        'trailerMaxCubage' => 'double',
        'trailerMaxWeight' => 'double',
    ];

    public function toChinaConsumption()
    {
        return $this->fuelConsumption()
            ->latest()
            ->whereHas('destination', function (Builder $query) {
                $query->where('name', '=', 'Китай');
            });
    }

    public function fuelConsumptions()
    {
        return $this->hasMany(FuelConsumption::class);
    }


    public function fuelConsumption()
    {
        return $this->hasOne(FuelConsumption::class)->latest();
    }

    public function fromChinaConsumption()
    {
        return $this->fuelConsumption()
            ->latest()
            ->whereHas('destination', function (Builder $query) {
                $query->where('name', '=', 'Таджикистан');
            });
    }

    public function trips()
    {
        return $this->hasMany(Trip::class, 'carId');
    }
}
