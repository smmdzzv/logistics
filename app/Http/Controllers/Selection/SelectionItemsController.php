<?php
/**
 *
 * @author Sultonazar Mamadazizov <sultonazar.mamadazizov@mail.ru> on 11.07.2020
 */

namespace App\Http\Controllers\Selection;


use App\Http\Controllers\BaseController;
use App\Models\StoredItems\ItemsSelection;

class SelectionItemsController extends BaseController
{
    public function index(ItemsSelection $selection)
    {
        return $selection->storedItems()->with('info.owner')->get();
    }
}
