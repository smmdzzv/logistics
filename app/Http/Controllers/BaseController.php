<?php

namespace App\Http\Controllers;


use App\Models\Branch;
use Illuminate\Database\Eloquent\Collection;

class BaseController extends Controller
{
    /**
     * @return int pagination limit
     */
    protected function pagination()
    {
        return request()->input('paginate') ?? 100;
    }

    protected function getBranches()
    {
        return auth()->user()->hasRole('admin') ? Branch::all() : new Collection([auth()->user()->branch]);
    }
}
