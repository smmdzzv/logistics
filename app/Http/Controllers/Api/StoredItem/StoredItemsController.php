<?php


namespace App\Http\Controllers\Api\StoredItem;


use App\Http\Controllers\Controller;
use App\Models\StoredItems\StoredItem;

class StoredItemsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
        $this->middleware('roles.allow:employee');
    }

    public function getItem()
    {
        return StoredItem::with('info.owner')->where('code', request('code'))->firstOrFail();
    }
}
