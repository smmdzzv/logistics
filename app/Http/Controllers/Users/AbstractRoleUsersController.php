<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;

/**
 * Controller for concrete users: driver, manager,etc.
 */
abstract class AbstractRoleUsersController extends Controller
{
    protected $entityClass;

    public function all()
    {
        $paginate = request()->paginate ?? 10;
        return $this->entityClass::with('roles')->paginate($paginate);
    }

    public function user($id)
    {
        return $this->entityClass::findOrFail($id);
    }

    /**
     * @return array of users that match provided userInfo
     */
    public function filter()
    {
        $info = request()->userInfo;

        return $this->entityClass::whereRaw("code LIKE '%$info%'")
            ->orWhereRaw("name LIKE '%$info%'")->roleConstraint()->get();
    }

    public function findByCode()
    {
        return $this->entityClass::where('code', request()->get('code'))->roleConstraint()->firstOrFail();
    }

    abstract protected function index();
}
