<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Models\Branch;
use App\Models\Currency;
use App\Models\Position;
use App\Models\Role;
use App\Models\Till\Account;
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

        $this->middleware('roles.allow:admin,manager')->except($except);
        $this->middleware('roles.deny:client')->only($except);
    }

    public function all()
    {
        return User::whereNotIn('code', ['1010011', '1010010'])->with('roles')->paginate(15);
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

        //Create account
        $account = new Account();
        $account->currency_id = Currency::where('isoName', 'USD')->first()->id;
        $account->balance = 0;
        $account->description = 'Долларовый счет пользователя ' . $user->name;

        $user->accounts()->save($account);

        return redirect(route('users.index'));
    }

    public function update(UserRequest $request, User $user)
    {
        $user->name = $request['name'];
        $user->email = $request['email'];
        $user->phone = $request['phone'];
        $user->code = $request['code'];
        if (isset($request['password']))
            $user->password = Hash::make($request['password']);

        $user->position_id = $request['position'] ? Position::firstOrCreate(['name' => $request['position']])->id : null;

        $user->roles()->detach(
            $user->roles()->where('name', '!=', 'admin')->get()
        );

        foreach ($request['roles'] as $id) {
            $role = Role::findOrFail($id);
            if ($role->name !== 'admin')
                $user->roles()->attach($role);
            elseif (auth()->user()->hasRole('admin') && !$user->hasRole('admin'))
                $user->roles()->attach($role);
        }

        if ($user->hasRole('admin') && !Role::whereIn('id', $request['roles'])->where('name', 'admin')->first())
            $user->roles()->detach(
                $user->roles()->where('name', '=', 'admin')->get()
            );

        if (!isset($user->branch->id) || $user->branch->id !== $request['branch']) {
            $branch = Branch::findOrFail($request['branch']);
            $user->branch()->associate($branch);
        }

        $user->save();
        return redirect(route('users.index'));
    }

    public function find()
    {
        $userInfo = request('userInfo');
        $users = User::whereRaw("name LIKE '%$userInfo%' OR code LIKE '%$userInfo%'")->get();

        $users = $users->filter(function ($user) {
            return count($user->roles) !== 0;
        });

        return array_values($users->all());
    }

    public function destroy(User $user){
        $user->delete();
        return;
    }
}
