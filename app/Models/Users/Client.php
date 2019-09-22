<?php

namespace App\Models\Users;

use App\Models\Order;
use App\Models\StoredItem;
use App\Models\Trip;
use App\User;
use Illuminate\Database\Eloquent\Builder;

class Client extends User
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
//        static::addGlobalScope('roles', function (Builder $builder) {
//            $builder->whereHas('roles', function ($query) {
//                $query->where('roles.name', 'client');
//            });
//        });
    }
    public function storedItems()
    {
        return $this->hasMany(StoredItem::class, 'ownerId');
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'ownerId');
    }
}
