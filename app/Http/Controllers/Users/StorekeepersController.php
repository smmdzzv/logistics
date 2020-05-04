<?php


namespace App\Http\Controllers\Users;


use App\Models\Users\Storekeeper;
class StorekeepersController extends AbstractRoleUsersController
{
    public function __construct()
    {
        $this->entityClass = Storekeeper::class;
    }

    public function index()
    {
        $url = 'concrete/storekeepers/all';
        $title = 'Список кладовщиков';
        return view('users.index', compact('url', 'title'));
    }
}
