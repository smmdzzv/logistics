<?php

use App\Http\Controllers\BaseController;

/**
 *
 * @author Sultonazar Mamadazizov <sultonazar.mamadazizov@mail.ru> on 10.07.2020
 */
class ClientItemsSelection extends BaseController
{
    public function index()
    {
        return ClientItemsSelection::latest()->paginate($this->pagination());
    }
}
