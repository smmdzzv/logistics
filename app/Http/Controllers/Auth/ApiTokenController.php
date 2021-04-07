<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ApiTokenController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api')->except('login');
    }

    /**
     * Authenticate user
     *
     * @param Request $request
     * @return mixed
     */
    public function login(Request $request){
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            if(!auth()->user()->api_token)
                auth()->user()->forceFill([
                    'api_token' => hash('sha256', Str::random(60)),
                ])->save();
            return ['token' => auth()->user()->api_token];
        }

        return response("Некорректные учетные данные", 403);
    }

    /**
     * Update the authenticated user's API token.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function update(Request $request)
    {
        $token = Str::random(60);

        $request->user()->forceFill([
            'api_token' => hash('sha256', $token),
        ])->save();

        return ['token' => $token];
    }
}
