<?php

namespace App\Http\Controllers;

use App\Tariff;
use Illuminate\Http\Request;

class OrdersController extends Controller
{
    public function create(){
        $user = auth()->user();
        auth()->user()->branch;
        $tariffs = Tariff::all();
        return view('orders.create', compact('user', 'tariffs'));
    }
}
