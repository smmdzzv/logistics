<?php

use App\Http\Controllers\BaseController;
use App\Models\StoredItems\ItemsSelection;

/**
 *
 * @author Sultonazar Mamadazizov <sultonazar.mamadazizov@mail.ru> on 10.07.2020
 */
class ClientItemsSelectionController extends BaseController
{
    public function index()
    {
        return ItemsSelection::latest()->paginate($this->pagination());
    }
}
