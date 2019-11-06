<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Models\Users\TrustedUser;
use Carbon\Carbon;

class TrustedUserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('roles.allow:admin');
    }

    private function rules()
    {
        return [
            'user_id' => 'required|string|exists:users,id',
            'from' => 'required|date',
            'to' => 'required|date',
            'maxDebt' => 'required|numeric|min:1'
        ];
    }

    public function index(){
        $trustedUsers = TrustedUser::with('user')
            ->where('to', '>=', Carbon::now()->toDateString())
            ->latest()
            ->paginate(10);
        return view('users.trusted-user.index', compact('trustedUsers'));
    }

    public function create()
    {
        return view('users.trusted-user.create');
    }

    public function store()
    {
        $data = request()->validate($this->rules());
        TrustedUser::create($data);
        return redirect(route('trusted-user.index'));
    }

    public function destroy(TrustedUser $trustedUser){
        $trustedUser->delete();
        return;
    }
}
