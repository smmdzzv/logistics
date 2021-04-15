<?php
/**
 * @author Sultonazar Mamadazizov <sultonazar.mamadazizov@mail.ru>
 */

namespace App\Http\Controllers\Api\Branch;


use App\Http\Controllers\Controller;
use App\Models\Branch;

class BranchesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
        $this->middleware('roles.allow:employee');
    }

    public function index(){
        return Branch::select('id', 'name')->get();
    }
}
