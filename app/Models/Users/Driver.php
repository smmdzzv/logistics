<?php

namespace App\Models\Users;

use App\Models\Trip;
use App\User;
use Illuminate\Database\Eloquent\Builder;

class Driver extends User
{
    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('roles', function (Builder $builder) {
            $builder->whereHas('roles', function ($query) {
                $query->where('roles.name', 'driver');
            });
        });
    }

    public function trips(){
        return $this->hasMany(Trip::class, 'driverId');
    }
}
