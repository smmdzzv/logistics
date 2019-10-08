<?php


namespace App\Models\Users;


use App\Scopes\Users\RoleScope;
use App\StoredItems\StorageHistory;
use App\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 *Base class for all employees
 */
class Employee extends User
{
    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new RoleScope);
    }

    public function getRoles()
    {
        return [
            'employee',
            'cashier',
            'driver',
            'director',
            'manager'
        ];
    }

    public function registeredItems(){
        return $this->hasMany(StorageHistory::class, 'registeredById');
    }
}
