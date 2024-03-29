<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Position;

class AppSettingsController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }


//Todo move to diff controllers
    public function storeBranch(){
        $data = request()->validate([
            'name' => ['required', 'string', 'max:255', 'unique:branches'],
            'user_id' => ['integer', 'nullable', 'exists:users']
        ]);

        Branch::create($data);

        return redirect('/settings');
    }

    public function storePosition(){
        $data = request()->validate([
            'name' => ['required', 'string', 'max:255', 'unique:positions'],
            'description' => ['text', 'nullable']
        ]);

        Position::create($data);
        return redirect('/settings');
    }

    public function show(){
        $branches = Branch::all();
        $positions = Position::all();
        return view('settings.show', compact('branches', 'positions'));
    }
}
