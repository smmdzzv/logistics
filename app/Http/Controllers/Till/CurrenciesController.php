<?php

namespace App\Http\Controllers\Till;

use App\Models\Country;
use App\Models\Currency;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CurrenciesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');

        $this->middleware('roles.allow:admin')->except('all');

        $this->middleware('roles.deny:client')->only('all');
    }

    private function rules(){
        return [
            'name' => 'required|unique:currencies',
            'shortName' => 'required|unique:currencies',
            'isoName' => 'required|unique:currencies',
            'country_id' => 'required|exists:countries,id'
        ];
    }

    public function all(){
        $paginate = request()->input('paginate') ?? 10;
        return Currency::with('country')->paginate($paginate);
    }

    public function index()
    {
        return view('till.currencies.index');
    }

    public function create()
    {
        $countries = Country::all();
        return view('till.currencies.create', compact('countries'));
    }

    public function store(Request $request)
    {
        Currency::create($request->validate($this->rules()));
        return redirect(route('currencies.index'));
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
