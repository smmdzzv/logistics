<?php


namespace App\Http\Controllers\Users;


use App\Models\Users\Manager;

class ManagersController extends AbstractRoleUsersController
{
    public function __construct()
    {
        $this->entityClass = Manager::class;
    }

    public function index()
    {
        $url = 'concrete/manager/all';
        $title = 'Список управляющих';
        return view('users.index', compact('url', 'title'));
    }
}
