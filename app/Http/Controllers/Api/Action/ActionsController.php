<?php
/**
 * @author Sultonazar Mamadazizov <sultonazar.mamadazizov@mail.ru>
 */

namespace App\Http\Controllers\Api\Action;


use App\Data\Dto\Actions\CarToCarDto;
use App\Http\Controllers\Controller;
use App\Models\Trip;
use App\Services\Action\CarToCarActionService;
use App\Services\Storage\ItemsStorageHistoryService;
use App\Services\StoredItem\Trip\StoredItemTripHistoryService;
use App\Services\Trip\LoadTripItemsService;

class ActionsController extends Controller
{
    private StoredItemTripHistoryService $tripHistoryService;
    private ItemsStorageHistoryService $storageHistoryService;

    public function __construct(
        StoredItemTripHistoryService $tripHistoryService,
        ItemsStorageHistoryService $storageHistoryService
    )
    {
        $this->tripHistoryService = $tripHistoryService;
        $this->storageHistoryService = $storageHistoryService;
    }

    public function store()
    {
        switch (request()->get('action')) {
            case 'branchToCar':
                $this->branchToCar();
                break;
            case 'carToCar':
                $this->carToCar();
                break;
            case 'carToBranch':
                $this->carToBranch();
                break;
            default:
                return response('Not found', 404);
        }
    }

    private function branchToCar()
    {
        $service = new LoadTripItemsService($this->tripHistoryService, $this->storageHistoryService);
        $service->load(
            Trip::findOrFail(
                request()->get('tripId')
            ),
            collect(request()->get('storedItems'))
        );
    }

    private function carToCar()
    {
        $service = new CarToCarActionService(
            new CarToCarDto(request()->all())
        );

        $service->transfer();
    }

    private function carToBranch(){

    }
}
