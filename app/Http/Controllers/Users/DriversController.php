<?php

namespace App\Http\Controllers\Users;

use App\Models\Users\Driver;

class DriversController extends AbstractRoleUsersController
{
    public function __construct()
    {
        $this->entityClass = Driver::class;
    }

    public function index()
    {
        $url = 'concrete/driver/all';
        $title = 'Список водителей';
        return view('users.index', compact('url', 'title'));
    }
}
