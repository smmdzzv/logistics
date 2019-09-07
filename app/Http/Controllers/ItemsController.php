<?php

namespace App\Http\Controllers;

use App\Item;
use Illuminate\Http\Request;

class ItemsController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }

    protected $validationRules=[
        'width' => 'required| max:3'
    ];

    public function all(){
        return Item::all();
    }

    public function validator(){
        $validator = JsValidator::make($this->validationRules);
        return $validator;
    }
}
