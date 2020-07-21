<?php
/**
 *
 * @author Sultonazar Mamadazizov <sultonazar.mamadazizov@mail.ru> on 13.07.2020
 */

namespace App\Http\Controllers\Trips;


use App\Http\Controllers\BaseController;
use App\Models\Customs\CustomsCode;
use App\Models\StoredItems\StoredItem;
use App\Models\StoredItems\StoredItemTripHistory;

class TripCustomsReportsController extends BaseController
{
    public function show($trip)
    {
        $storedItems = StoredItem::with('info.owner', 'info.customsCode.tax','info.customsCodeTax', 'info.billingInfo')
            ->whereHas('tripHistory', function ($query) use ($trip) {
                $query->withTrashed()->where('trip_id', $trip)
                    ->where('status', StoredItemTripHistory::STATUS_LOADED)
                    ->orWhere('status', StoredItemTripHistory::STATUS_COMPLETED);
            })->get();

        $customCodes = CustomsCode::with('tax')->get();

        return view('trips.customs-report.show', compact('storedItems', 'customCodes'));
    }
}
