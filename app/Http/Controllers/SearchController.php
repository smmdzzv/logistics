<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\User;
use App\Common\ResponseFactory;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class SearchController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }

    public function findUsersByInfo($userInfo){
        $users = User::whereRaw("name LIKE '%$userInfo%' OR code LIKE '%$userInfo%'")->get();

        $users = $users->filter(function ($user){
            return count($user->roles) !== 0;
        });

        return array_values($users->all());
    }
}
