<?php


namespace App\Models\Users;


use App\Models\Order;
use App\Models\Position;
use App\StoredItems\StorageHistory;

/**
 *Base class for all employees
 */
class Employee extends RoleUser
{
    public function getRoles()
    {
        return [
            'worker',
            'cashier',
            'driver',
            'director',
            'manager'
        ];
    }

    public function position()
    {
        return $this->belongsTo(Position::class);
    }

    public function registeredItems()
    {
        return $this->hasMany(StorageHistory::class, 'registeredById');
    }

    public function registeredOrders()
    {
        return $this->hasMany(Order::class, 'registeredBy');
    }
}
