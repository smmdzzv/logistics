<?php
/**
 * @author Sultonazar Mamadazizov <sultonazar.mamadazizov@mail.ru>
 */

namespace App\Services\Trip;


use App\Models\Trip;

class TripService
{
    function getAvailableTrips(){
        return Trip::whereIn('status',[
            Trip::STATUS_ACTIVE,
            Trip::STATUS_SCHEDULED
        ])->latest()->get();
    }
}
