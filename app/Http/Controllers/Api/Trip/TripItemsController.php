<?php
/**
 * @author Sultonazar Mamadazizov <sultonazar.mamadazizov@mail.ru>
 */

namespace App\Http\Controllers\Api\Trip;


use App\Models\StoredItems\StoredItem;
use App\Models\Trip;
use App\Services\Trip\TripService;

class TripItemsController
{
    public function __construct(TripService $tripService)
    {
        $this->middleware('auth:api');
        $this->middleware('roles.allow:employee');
    }

    public function index(Trip $trip)
    {
        return $trip->storedItems()
            ->select(['stored_items.id', 'stored_item_info_id', 'code', 'stored_items.status'])
            ->whereIn('stored_items.status', [
                StoredItem::STATUS_STORED,
                StoredItem::STATUS_TRANSIT
            ])->get();
    }
}
