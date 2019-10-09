<?php


namespace App\Http\Controllers\Users;


use App\Models\Users\Employee;

class EmployeesController extends AbstractRoleUsersController
{
    public function __construct()
    {
        $this->entityClass = Employee::class;
    }

    public function index()
    {
        $url = 'concrete/employee/all';
        $title = 'Список сотрудников';
        return view('users.index', compact('url', 'title'));
    }
}
