<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Role;
use App\User;
use Illuminate\Validation\Rule;

/**
    Controller for concrete users: driver, manager,etc.
 */
abstract class AbstractRoleUsersController extends Controller
{
    abstract protected function getRoleName();

    protected function getRole(){
        return Role::where('name', $this->getRoleName());
    }

    public function findUser($userInfo){
        $users = $this->getRole()->users()->whereRaw("name LIKE '%$userInfo%' OR code LIKE '%$userInfo%'")->get();

        return array_values($users->all());
    }

}
