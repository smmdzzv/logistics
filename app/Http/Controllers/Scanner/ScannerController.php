<?php
namespace App\Http\Controllers\Scanner;

use App\Http\Controllers\BaseController;
use App\Models\StoredItems\ItemsSelection;

/**
 *
 * @author Sultonazar Mamadazizov <sultonazar.mamadazizov@mail.ru> on 07.07.2020
 */
class ScannerController extends BaseController
{
    public function index()
    {
        $selections = ItemsSelection::latest()->limit(30)->get();
        return view('scanner.index', compact('selections'));
    }
}
