<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Models\Role;
use PhpParser\Builder;

/**
    * Controller for concrete users: driver, manager,etc.
 */
abstract class AbstractConcreteUsersController extends Controller
{
    abstract protected function getRoleName();

    abstract protected function getRoleNamePlural();

    abstract protected function getClassName();

    protected function getRole(){
        $role =  Role::where('name', $this->getRoleName())->first();
        if(!isset($role))
            abort(404, 'Указанная роль не найдена');
        return $role;
    }

    public function all(){
        $relation = $this->getRoleNamePlural();
        $class = $this->getClassName();
        return $this->getRole()->concreteUsers($class);
    }

    public function concreteUser($id){
        return $this->getClassName()::findOrFail($id);
    }

    public function filter(){
        $userInfo = request()->userInfo;
        $class= $this->getClassName();
        $roleId = $this->getRole()->id;

        $users = $class::whereHas('roles', function ($query) use($roleId){
            $query->where('roles.id', '=', $roleId);
        })
//            ->where('code', 'like', '%$userInfo%')->orWhere('name', 'like', '%$userInfo%')->get();
            ->whereRaw("code LIKE '%$userInfo%'")->orWhereRaw("name LIKE '%$userInfo%'")->get();

        return array_values($users->all());
    }
}
