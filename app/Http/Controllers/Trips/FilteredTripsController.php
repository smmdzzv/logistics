<?php
/**
 *
 * @author Sultonazar Mamadazizov <sultonazar.mamadazizov@mail.ru> on 08.07.2020
 */

namespace App\Http\Controllers\Trips;


use App\Data\Filters\TripFilter;
use App\Http\Controllers\BaseController;
use App\Models\Trip;

class FilteredTripsController extends BaseController
{
    public function index()
    {
        $filter = new TripFilter(request()->all(), Trip::query());
        return $filter->filter()->get();
    }
}
