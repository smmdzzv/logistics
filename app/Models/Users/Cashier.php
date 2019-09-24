<?php


namespace App\Models\Users;


use App\User;
use Illuminate\Database\Eloquent\Builder;

class Cashier extends User
{
    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        //Todo make local
        static::addGlobalScope('roles', function (Builder $builder) {
            $builder->whereHas('roles', function ($query) {
                $query->where('roles.name', 'cashier');
            });
        });
    }
}
