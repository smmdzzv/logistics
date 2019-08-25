<?php

namespace App\Http\Controllers;

use App\Role;
use App\User;
use App\Common\ResponseFactory;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class SearchController extends Controller
{
    public function findUsersByInfo($userInfo){
        $users = User::whereRaw("name LIKE '%$userInfo%' OR code LIKE '%$userInfo%'")->get();

        $users = $users->filter(function ($user){
            return count($user->clientRole) !== 0;
        });

        return array_values($users->all());
    }
}
