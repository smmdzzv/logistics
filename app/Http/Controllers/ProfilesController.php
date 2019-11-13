<?php

namespace App\Http\Controllers;

use App\Models\Users\Client;
use App\User;

class ProfilesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function show(Client $user)
    {
        if ($user->cant('update', $user))
            abort(403, 'Недостаточно прав для просмотра профиля');
        $user->load('accounts.currency', 'activeOrders.owner', 'activeOrders.registeredBy');
        return view('users.profile', compact('user'));
    }
}
