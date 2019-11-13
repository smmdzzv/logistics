<?php

namespace App\Policies;

use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    public function before(User $user, $ability)
    {
        return $user->hasRole('admin');
    }

    public function update(User $authorizedUser, User $user)
    {
        return $authorizedUser->id === $user->id;
    }
}
