<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BaseController extends Controller
{
    /**
     * @return int pagination limit
     */
    protected function pagination(){
        return request()->input('paginate') ?? 10;
    }
}
