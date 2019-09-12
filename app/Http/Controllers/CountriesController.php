<?php

namespace App\Http\Controllers;

use App\Models\Country;

class CountriesController extends Controller
{
    public function all(){
        return Country::all();
    }
}
