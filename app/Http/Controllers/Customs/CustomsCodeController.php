<?php

namespace App\Http\Controllers\Customs;

use App\Http\Controllers\Controller;
use App\Models\Customs\CustomsCode;
use Illuminate\Http\Request;

class CustomsCodeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('roles.allow:admin');
    }

    private function rules(){
        return [
            'name' => 'required|string|max:255',
            'shortName' => 'required|string|max:255',
            'internationalName' => 'nullable|string|max:255',
            'code' => 'required|string|max:15',
            'price' => 'required|numeric',
            'rate' => 'required|numeric',
            'percentage' => 'required|numeric',
            'vat' => 'required|numeric',
            'calculateByPiece' => 'required'
        ];
    }

        public function index(){
            $codes = CustomsCode::all();
            return view('customs.index', compact('codes'));
        }

        public function create(){
            return view('customs.create');
        }

        public function store(Request $request){
            $data = $request->validate($this->rules());
            CustomsCode::create($data);
            return redirect(route('customs-code.index'));
        }
}
