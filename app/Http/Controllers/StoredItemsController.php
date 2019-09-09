<?php

namespace App\Http\Controllers;

use App\Branch;
use App\StoredItem;
use Illuminate\Http\Request;

class StoredItemsController extends Controller
{
    public function index(){
        $branches = Branch::all();
        return view('stored.index', compact('branches'));
    }

    public function all(){
        return StoredItem::with(['owner','item'])->paginate(10);
    }

    public function filteredByBranch(Branch $branch){
        if(isset($branch)){
            return $branch->storedItems()->with(['owner','item'])->paginate(10);
        }
        else abort(404, 'Филиал не найден');
    }
}
