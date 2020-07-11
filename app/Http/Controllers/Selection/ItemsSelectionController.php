<?php
namespace App\Http\Controllers\Selection;

use App\Http\Controllers\BaseController;
use App\Models\StoredItems\ItemsSelection;
use App\Services\Selection\ItemsSelectionService;
use App\User;
use Carbon\Carbon;

/**
 *
 * @author Sultonazar Mamadazizov <sultonazar.mamadazizov@mail.ru> on 10.07.2020
 */
class ItemsSelectionController extends BaseController
{
    private ItemsSelectionService $service;

    public function __construct(ItemsSelectionService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        return ItemsSelection::latest()->paginate($this->pagination());
    }

    public function store()
    {
        return $this->service->store(
            User::findOrFail(request()->get('user_id', auth()->id())),
            collect(request()->get('storedItems')),
            request()->get('name', 'Выборка пользователя')
        );
    }
}
