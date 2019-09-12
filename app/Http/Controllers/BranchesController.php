<?php

namespace App\Http\Controllers;

use App\Branch;
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
            'nullable',
            'exists:users,id'
        ],
    ];

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function all()
    {
        return Branch::with('director')->get();
    }

    public function show()
    {

    }

    public function create()
    {
        return view('branches.create');
    }

    public function edit()
    {

    }

    public function store()
    {
        $data = request()->validate($this->rules);
        $branch = Branch::create($data);
        $branch->load('director');
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

        $branch->load('director');
        return $branch;

    }

    public function destroy()
    {

    }

}
