<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TripsController extends Controller
{
    public function create(){
        return view('trips.create');
    }
}
