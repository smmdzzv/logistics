<?php


namespace App\Http\Controllers\Users;


use App\Models\Users\Director;

class DirectorsController extends AbstractRoleUsersController
{
    public function __construct()
    {
        $this->entityClass = Director::class;
    }

    public function index()
    {
        $url = 'concrete/director/all';
        $title = 'Список директоров';
        return view('users.index', compact('url', 'title'));
    }
}
