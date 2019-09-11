<?php

namespace App\Http\Controllers;

use App\Branch;
use App\Position;
use App\Role;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UsersController extends Controller
{

    public function __construct(){
        $this->middleware('auth');
    }

    protected function rules (){
        return $rules = [
        'name' => 'required|string|max:255',
        'roles' => 'required|array',
        'branch' => 'required|exists:branches,id',
        'position' => 'nullable|string',
        'phone' => 'nullable|string',
        'code' => 'nullable|string'
        ];
    }

    public function create(){
        $branches = Branch::all();
        $roles = $this->getRoles();

        return view('auth/register', compact('branches', 'roles'));
    }

    public function edit(User $user){
        $branches = Branch::all();
        $roles = $this->getRoles();
        $user->load(['roles', 'branch']);
        return view('users.edit', compact('branches', 'roles', 'user'));
    }

    //TODO validation
    public function store(){
        $rules = $this->rules();
        $rules['password'] = 'required|string|min:8|confirmed';
        $rules['email'] = 'required|string|email|max:255|unique:users';
        $data = Validator::make(request()->all(), $rules)->validate();
        $positionId = $data['position']? Position::firstOrCreate(['name'=> $data['position']]) -> id : null;
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
        foreach ($roles as $role){
            if(in_array($role->id, $data['roles']))
                $role->users()->attach($user);
        }

        return redirect(route('home'));
    }

    public function update(User $user){
        $rules = $this->rules();
        if(request()->get('password') !== null)
            $rules['password'] = 'required|string|min:8|confirmed';
        if(request()->get('email') !== $user->email)
            $rules['email'] = 'required|string|email|max:255|unique:users';
        $data = Validator::make(request()->all(), $rules)->validate();

        $user->name = $data['name'];
        $user->email = $data['email'] ?? $user->email;
        $user->phone = $data['phone'];
        if(isset($data['password']))
            $user->password = Hash::make($data['password']);

        $user->position_id = $data['position']? Position::firstOrCreate(['name'=> $data['position']])->id : null;

        $user->roles()->detach();
        foreach ($data['roles'] as $id) {
            $role = Role::findOrFail($id);
            if($role->name !== 'admin')
                $user->roles()->attach($role);
            elseif(auth()->user()->hasRole('admin'))
                $user->roles()->attach($role);
        }

        if(!isset($user->branch->id) || $user->branch->id !== $data['branch']){
            $branch = Branch::findOrFail($data['branch']);
            $user->branch()->associate($branch);
        }

        $user->save();
        return redirect(route('home'));
    }

    public function employees(){
        return view('users.index', ['type' => 'employee']);
    }

    public function clients(){
        return view('users.index', ['type' => 'client']);
    }

    public function filtered($role){
        $users = Role::where('name',$role)->first() ->users()->paginate(10);
        $users->load(['position', 'roles']);
        return $users;
    }

    private function getRoles(){
        $roles = array();
        if(auth()->user()->hasRole('admin'))
            $roles = Role::all();
        else
            $roles = Role::where('name', '!=', 'admin')->get();
        return $roles;
    }
}
