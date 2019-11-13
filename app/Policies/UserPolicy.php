<?php

namespace App\Policies;

use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    public function before(User $user, $ability)
    {
        if($user->hasRole('admin')){
            return true;
        }
    }

    public function update(User $authorizedUser, User $user)
    {
        return $authorizedUser->id === $user->id;
    }
}
