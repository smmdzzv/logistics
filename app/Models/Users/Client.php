<?php

namespace App\Models\Users;

use App\Models\Order;
use App\Models\StoredItem;
use App\Models\Trip;
use App\User;

class Client extends User
{
    public function storedItems()
    {
        return $this->hasMany(StoredItem::class, 'ownerId');
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'ownerId');
    }
}
