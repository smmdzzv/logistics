<?php


namespace App\Models\Users;


use App\Scopes\Users\RoleScope;
use App\User;

abstract class RoleUser extends User
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

    abstract function getRoles();
}
