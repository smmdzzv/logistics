<?php

namespace App\Models\Users;

use App\Models\Order;
use App\Models\StoredItems\StoredItem;
use mysql_xdevapi\Collection;

/**
 * @property Collection activeOrders
 */
class Client extends RoleUser
{
    public function getRoles()
    {
        return [
            'client'
        ];
    }

    public function storedItems()
    {
        return $this->hasMany(StoredItem::class, 'ownerId');
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'ownerId');
    }

    public function unpaidOrders(){
        return $this->orders()->where('paymentId',  null);
    }

    public function activeOrders(){
        return $this->orders()->where('status','!=',"finished");
    }
}
