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
 * @property double fuelAmount
 * @property string id
 * @property string car_provider_id
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
        'fuelAmount' => 'double'
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

    public function storeDefaultConsumptions(){
        $toChina = new FuelConsumption();
        $toChina->forEmpty = 0.4;
        $toChina->forLoaded = 0.42;
        $toChina->forEmptyTrailer = 0.42;
        $toChina->forLoadedTrailer = 0.42;
        $toChina->destination_id = Country::where('name', 'Китай')->first()->id;
        $toChina->car_id = $this->id;
        $toChina->save();


        $fromChina = new FuelConsumption();
        $fromChina->forEmpty = 0.39;
        $fromChina->forLoaded = 0.40;
        $fromChina->forEmptyTrailer = 0.42;
        $fromChina->forLoadedTrailer = 0.42;
        $fromChina->destination_id = Country::where('name', 'Таджикистан')->first()->id;;
        $fromChina->car_id = $this->id;
        $fromChina->save();
    }

    public function trips()
    {
        return $this->hasMany(Trip::class, 'carId');
    }

    public function provider(){
        return $this->belongsTo(CarProvider::class, 'car_provider_id');
    }
}
