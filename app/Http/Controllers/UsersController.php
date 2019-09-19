<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Position;
use App\Models\Role;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UsersController extends Controller
{

    private $rules = [
        'name' => 'required|string|max:255',
        'roles' => 'required|array',
        'branch' => 'required|exists:branches,id',
        'position' => 'nullable|string',
        'phone' => 'nullable|string',
        'email' => 'required|string|email|max:255|unique:users',
        'password' => 'required|string|min:8|confirmed',
        'code' => 'nullable|string|unique:users'
    ];

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function create()
    {
        $branches = Branch::all();
        $roles = $this->getRoles();

        return view('auth/register', compact('branches', 'roles'));
    }

    private function getRoles()
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

    public function store()
    {
        $data = Validator::make(request()->all(), $this->rules)->validate();
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

        return redirect(route('home'));
    }

    public function update(User $user)
    {
        $rules = [
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id)
            ],
            'code' =>[
                'nullable',
                'string',
                Rule::unique('users')->ignore($user->id)
            ],
            'password' =>[
                'nullable',
                'string',
                'min:8',
                'confirmed'
            ]
        ];

        $rules = array_merge($this->rules, $rules);

        $data = request()->validate($rules);

        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->phone = $data['phone'];
        if (isset($data['password']))
            $user->password = Hash::make($data['password']);

        $user->position_id = $data['position'] ? Position::firstOrCreate(['name' => $data['position']])->id : null;

        $user->roles()->detach();
        foreach ($data['roles'] as $id) {
            $role = Role::findOrFail($id);
            if ($role->name !== 'admin')
                $user->roles()->attach($role);
            elseif (auth()->user()->hasRole('admin'))
                $user->roles()->attach($role);
        }

        if (!isset($user->branch->id) || $user->branch->id !== $data['branch']) {
            $branch = Branch::findOrFail($data['branch']);
            $user->branch()->associate($branch);
        }

        $user->save();
        return redirect(route('home'));
    }

    public function employees()
    {
        return view('users.index', ['type' => 'employee']);
    }

    public function clients()
    {
        return view('users.index', ['type' => 'client']);
    }

    public function filtered($role)
    {
        $users = Role::where('name', $role)->first()->users()->with('position', 'roles')->paginate(10);
        return $users;
    }
}
