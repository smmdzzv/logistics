<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class OrdersController extends Controller
{
    public function create(){
        $user = auth()->user();
        auth()->user()->branch;
        return view('orders.create', compact('user'));
    }
}
