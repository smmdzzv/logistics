<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Models\Branch;
use App\Models\Position;
use App\Models\Role;
use App\User;
use Illuminate\Support\Facades\Hash;

/**
 * CRUD controller for user
 */
class UsersController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');

        $except = ['all', 'index'];

        $this->middleware('roles.allow:admin,director')->except($except);
        $this->middleware('roles.deny:client')->only($except);
    }

    public function all()
    {
        return User::with('roles')->paginate(10);
    }

    public function index()
    {
        $roles = Role::all();
        $title = "Список пользователей";
        $url = route('users.all');
        return view('users.index', compact('roles', 'title', 'url'));
    }

    public function create()
    {
        $branches = Branch::all();
        $roles = $this->getRoles();

        return view('auth/register', compact('branches', 'roles'));
    }

    /**
     * @return Role[]|array
     */
    protected function getRoles()
    {
        $roles = array();
        if (auth()->user()->hasRole('admin'))
            $roles = Role::all();
        else
            $roles = Role::where('name', '!=', 'admin')->get();
        return $roles;
    }

    //TODO validation

    public function edit(User $user)
    {
        $branches = Branch::all();
        $roles = $this->getRoles();
        $user->load('roles', 'branch');
        return view('users.edit', compact('branches', 'roles', 'user'));
    }

    public function store(UserRequest $request)
    {
        $data = $request->all();
        $positionId = $data['position'] ? Position::firstOrCreate(['name' => $data['position']])->id : null;
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'code' => $data['code'],
            'password' => Hash::make($data['password']),
            'position_id' => $positionId,
            'branch_id' => $data['branch']
        ]);

        $roles = $this->getRoles();
        foreach ($roles as $role) {
            if (in_array($role->id, $data['roles']))
                $role->users()->attach($user);
        }

        return $user;
    }

    public function update(UserRequest $request, User $user)
    {
        $user->name = $request['name'];
        $user->email = $request['email'];
        $user->phone = $request['phone'];
        $user->code = $request['code'];
        if (isset($data['password']))
            $user->password = Hash::make($request['password']);

        $user->position_id = $request['position'] ? Position::firstOrCreate(['name' => $request['position']])->id : null;

        $user->roles()->detach();

        foreach ($request['roles'] as $id) {
            $role = Role::findOrFail($id);
            if ($role->name !== 'admin')
                $user->roles()->attach($role);
            elseif (auth()->user()->hasRole('admin'))
                $user->roles()->attach($role);
        }

        if (!isset($user->branch->id) || $user->branch->id !== $request['branch']) {
            $branch = Branch::findOrFail($request['branch']);
            $user->branch()->associate($branch);
        }

        $user->save();
        return redirect(route('home'));
    }

}
