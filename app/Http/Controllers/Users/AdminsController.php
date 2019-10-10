<?php


namespace App\Http\Controllers\Users;


use App\Models\Users\Admin;
use Illuminate\Support\Facades\Auth;

class AdminsController extends AbstractRoleUsersController
{
    public function __construct()
    {
        $this->entityClass = Admin::class;
    }

    public function index()
    {
        $this->adminCheck();

        $url = 'concrete/admin/all';
        $title = 'Список администраторов';
        return view('users.index', compact('url', 'title'));
    }

    private function adminCheck()
    {
        if (!auth()->user()->hasRole('admin'))
            return abort(403, 'Доступ запрещен');
    }

    public function all()
    {
        $this->adminCheck();
        return parent::all();
    }

    public function filter()
    {
        $this->adminCheck();
        return parent::filter();
    }
}
