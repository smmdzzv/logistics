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

    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'roles' => ['required', 'array'],
            'branch' => ['required', 'exists:branches,id'],
            'position-name' => 'nullable|string'
        ]);
    }

    public function create(){
        $branches = Branch::all();
        $roles = $this->getRoles();

        return view('auth/register', compact('branches', 'roles'));
    }

    //TODO validation
    public function store(){
        $data = $this->validator(request()->all())->validate();
        $positionId = $data['position-name']? Position::firstOrCreate(['name'=> $data['position-name']]) -> id : null;

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
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

    private function attachRoles($user){

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
