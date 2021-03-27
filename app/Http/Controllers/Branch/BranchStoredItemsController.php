<?php

namespace App\Http\Controllers\Branch;

use App\Http\Controllers\BaseController;
use App\Models\Branch;
use App\Services\StoredItem\Status\BranchStoredItemsService;

/**
 *
 * @author Sultonazar Mamadazizov <sultonazar.mamadazizov@mail.ru> on 08.07.2020
 */
class BranchStoredItemsController extends BaseController
{
    public BranchStoredItemsService $service;

    public function __construct(BranchStoredItemsService $service)
    {
        $this->service = $service;
    }

    public function store(Branch $branch)
    {
        $this->service->store(collect(request()->get('storedItems')), $branch);
    }
}
