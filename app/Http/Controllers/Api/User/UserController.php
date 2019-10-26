<?php

namespace App\Http\Controllers\Api\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
        $this->middleware('roles.allow:employee');
    }

    public function authenticated(Request $request){
        return $request->user()->load('branch');
    }
}
