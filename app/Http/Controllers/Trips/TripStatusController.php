<?php
/**
 *
 * @author Sultonazar Mamadazizov <sultonazar.mamadazizov@mail.ru> on 10.07.2020
 */

namespace App\Http\Controllers\Trips;


use App\Http\Controllers\BaseController;
use App\Models\Trip;
use App\Services\Trip\TripStatusService;

class TripStatusController extends BaseController
{
    private TripStatusService $service;

    public function __construct(TripStatusService $service)
    {
        $this->service = $service;
    }

    public function update(Trip $trip)
    {
        switch (request()->get('status')) {
            case Trip::STATUS_ACTIVE:
                $this->service->setActive($trip);
                break;
            case Trip::STATUS_COMPLETED:
                $this->service
                    ->setCompleted($trip, (float)request()->get('mileageAfter'), (float)request()->get('consumption'));
                break;
            case Trip::STATUS_SCHEDULED:
                $this->service->cancelActiveStatus($trip);
                break;
        }

        return redirect(route('trips.show', $trip));
    }
}
