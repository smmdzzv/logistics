<?php


namespace App\Models\Users;


use App\User;

abstract class RoleUser extends User
{ 
    abstract function getRoles();

    public function scopeRoleConstraint($query)
    {
        $roles = $this->getRoles();

        return $query->whereHas('roles', function ($query) use ($roles) {
            $query->whereIn('roles.name', $roles);
        });
    }

    public function getMorphClass()
    {
        return 'users';
    }
}
