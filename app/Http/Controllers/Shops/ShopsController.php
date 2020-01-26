<?php

namespace App\Http\Controllers\Shops;

use App\Shops\Shop;
use App\Http\Controllers\Controller;

class ShopsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('roles.allow:admin');
    }

    public function create(){
        return view('shops.create');
    }

    public function store(){
        Shop::create(request()->all());
        return redirect(route('shop.create'));
    }
}
