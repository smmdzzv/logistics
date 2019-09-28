<?php

namespace App\Http\Controllers\Till;

use App\Models\Currency;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CurrenciesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');

        $this->middleware('role:admin');
    }

    private function rules(){
        return [
            'name' => 'required|unique:currencies',
            'shortName' => 'required|unique:currencies',
            'isoName' => 'required|unique:currencies'
        ];
    }

    public function index()
    {
        //TODO not implemented
    }

    public function create()
    {
        return view('till.currencies.create');
    }

    public function store(Request $request)
    {
        return Currency::create($request->validate($this->rules()));
    }

    public function show($id)
    {
        //TODO not implemented
    }

    public function edit($id)
    {
        //TODO not implemented
    }

    public function update(Request $request, $id)
    {
        //TODO not implemented
    }

    public function destroy($id)
    {
        //TODO not implemented
    }
}
