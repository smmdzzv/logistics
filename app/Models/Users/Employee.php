<?php


namespace App\Models\Users;


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

    public function registeredItems()
    {
        return $this->hasMany(StorageHistory::class, 'registeredById');
    }
}
