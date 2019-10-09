<?php


namespace App\Http\Controllers\Users;


use App\Models\Users\Worker;

class WorkersController extends AbstractRoleUsersController
{
    public function __construct()
    {
        $this->entityClass = Worker::class;
    }

    public function index()
    {
        $url = 'concrete/worker/all';
        $title = 'Список рабочих';
        return view('users.index', compact('url', 'title'));
    }
}
