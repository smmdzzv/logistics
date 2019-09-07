<?php

namespace App\Http\Controllers;

use App\Branch;
use Illuminate\Http\Request;

class BranchesController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }

    public function all(){
        return Branch::all();
    }
}
