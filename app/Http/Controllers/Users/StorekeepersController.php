<?php


namespace App\Http\Controllers\Users;


use App\Models\Users\Worker;

class StorekeepersController extends AbstractRoleUsersController
{
    public function __construct()
    {
        $this->entityClass = Worker::class;
    }

    public function index()
    {
        $url = 'concrete/storekeepers/all';
        $title = 'Список кладовщиков';
        return view('users.index', compact('url', 'title'));
    }
}
