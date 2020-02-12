<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Customs\CustomsCode;
use App\Models\StoredItems\Item;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ItemsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');

        $except = ['all', 'allEager'];

        $this->middleware('roles.allow:admin')->except($except);
        $this->middleware('roles.deny:client')->only($except);
    }

    public function all()
    {
        $paginate = request()->paginate ?? 20;
        return Item::paginate($paginate);
    }

    public function allEager()
    {
        return Item::with('codes')->get();
    }

    public function index()
    {
        return view('items.index');
    }

    public function create()
    {
//        $branches = Branch::all();
        $customsCodes = CustomsCode::all();
        return view('items.create', compact( 'customsCodes'));
    }

    public function store(Request $request)
    {
        $data = $request->validate($this->rules());
        $item = Item::create($data);
        $item->codes()->attach($request->get('customsCodes'));
        return redirect()->route('items.index');
    }

    private function rules()
    {
        return [
            'unit' => 'required|string|max:10',
            'onlyCustomPrice' => "required|in:0,1",
            'applyDiscount' => "required|in:0,1",
            'onlyAgreedPrice' => "required|in:0,1",
            'calculateByNormAndWeight' => "required|in:0,1",
//            'branch_id' => 'required|exists:branches,id',
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('items')->ignore(request()->get('id'))
            ]
        ];
    }
}
