<?php

namespace App\Http\Controllers;

class BaseController extends Controller
{
    /**
     * @return int pagination limit
     */
    protected function pagination(){
        return request()->input('paginate') ?? 10;
    }
}
