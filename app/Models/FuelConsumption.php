<?php

namespace App\Models;


/**
 * @property double forEmpty
 * @property double forLoaded
 * @property double forEmptyTrailer
 * @property double forLoadedTrailer
 */
class FuelConsumption extends BaseModel
{
    protected $guarded = [];

    protected $casts = [
        'forEmpty' => 'double',
        'forLoaded' => 'double',
        'forEmptyTrailer' => 'double',
        'forLoadedTrailer' => 'double'
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->forEmpty = $this->forEmpty ?? 0;
        $this->forLoaded = $this->forLoaded ?? 0;
        $this->forEmptyTrailer = $this->forEmptyTrailer ?? 0;
        $this->forLoadedTrailer = $this->forLoadedTrailer ?? 0;
    }

    public function car(){
        return $this->belongsToMany(Car::class);
    }

    public function destination(){
        return $this->belongsTo(Country::class);
    }
}
