<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Tariff;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ItemsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');

        $this->middleware('role:admin', ['except' => 'all']);
    }

    public function all()
    {
        return Item::all();
    }

    public function create()
    {
        $tariffs = Tariff::all();
        return view('items.create', compact('tariffs'));
    }

    public function store(Request $request)
    {
        $data = $request->validate($this->rules());
        Item::create($data);
        return redirect()->route('items.create');
    }

    private function rules()
    {
        return [
            'unit' => 'required|string|max:10',
            'onlyCustomPrice' => "required|in:0,1",
            'applyDiscount' => "required|in:0,1",
            'tariffId' => 'required|exists:tariffs,id',
            'name' =>[
                'required',
                'string',
                'max:255',
                Rule::unique('items')->ignore(request()->get('id'))
            ]
        ];
    }
}
