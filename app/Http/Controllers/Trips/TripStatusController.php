<?php
/**
 *
 * @author Sultonazar Mamadazizov <sultonazar.mamadazizov@mail.ru> on 10.07.2020
 */

namespace App\Http\Controllers\Trips;


use App\Models\Trip;
use App\Services\Trip\TripStatusService;

class TripStatusController
{
    private TripStatusService $service;

    public function __construct(TripStatusService $service)
    {
        $this->service = $service;
    }

    public function update(Trip $trip)
    {
        $this->service->setActive($trip);
        return;
    }
}
