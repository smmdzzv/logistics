<?php

namespace App\Http\Controllers\Till;

use App\Models\Till\Expenditure;
use App\Models\Trip;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Validation\Rule;

class ExpendituresController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin');
    }

    public function rules()
    {
        return [
            'description' => 'nullable|string|max:500',
            'type' => [
                'required',
                Rule::in(['in', 'out'])
            ],
            'title' => [
                'required',
                Rule::unique('expenditures')->ignore(request()->get('id'))
            ]
        ];
    }

    public function all()
    {
        $paginate = request()->paginate ?? 10;
        return Expenditure::paginate($paginate);
    }

    public function index()
    {
        return view('till.expenditures.index');
    }

    function create()
    {
        return view('till.expenditures.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate($this->rules());
        Expenditure::create($data);
        return redirect(route('expenditures.index'));
    }

    public function edit(Expenditure $expenditure)
    {
        return view('till.expenditures.edit', compact('expenditure'));
    }

    public function update(Request $request, Expenditure $expenditure)
    {
        $data = $request->validate($this->rules());
        $expenditure->fill($data);
        $expenditure->save();
        return redirect(route('expenditures.index'));
    }

    public function destroy(Expenditure $expenditure)
    {
       $expenditure->delete();
       return;
    }
}
