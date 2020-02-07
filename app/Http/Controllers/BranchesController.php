<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Currency;
use App\Models\Till\Account;
use Carbon\Carbon;
use Illuminate\Validation\Rule;

class BranchesController extends Controller
{

    private $rules = [
        'name' => [
            'required',
            'string',
            'max:255',
            'unique:branches,name'],
        'country' => [
            'required',
            'exists:countries,id'
        ],
        'director' => [
            'sometimes',
            'exists:users,id'
        ],
    ];

    public function __construct()
    {
        $this->middleware('auth');

        $this->middleware('roles.allow:admin');
    }

    public function all()
    {
        return Branch::with('director', 'country')->get();
    }

    public function index()
    {
        return view('branches.index');
    }

    public function store()
    {
        $data = request()->validate($this->rules);

        $branch = Branch::create($data);
        if (!isset($data['director']))
            $branch->director = null;
        $branch->load('director', 'country');

        $accounts = [];
        $currencies = Currency::all();
        foreach ($currencies as $currency){
            $accounts[] = new Account([
                'balance' => 0,
                'description' => "Cчет {$branch->name} {$currency->isoName} ({$currency->name})",
                'currencyId' => $currency->id
            ]);
        }

        $branch->accounts()->saveMany($accounts);

        return $branch;
    }

    public function update(Branch $branch)
    {
        $rules = [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('branches')->ignore($branch->id)]
        ];

        $rules = array_merge($this->rules, $rules);
        $data = request()->validate($rules);

        $branch->fill($data);
        $branch->save();
        $branch->load('director', 'country');
        return $branch;

    }

    public function destroy(Branch $branch)
    {
        $branch->deleted_by_id = auth()->id();
        $branch->deleted_at = Carbon::now();
        $branch->save();
        return;
    }

}
