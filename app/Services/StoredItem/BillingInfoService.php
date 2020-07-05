<?php
/**
 *
 * @author Sultonazar Mamadazizov <sultonazar.mamadazizov@mail.ru> on 05.07.2020
 */

namespace App\Services\StoredItem;


use App\Data\MassWriters\Order\BillingInfosMassWriter;
use App\Models\StoredItems\StoredItemInfo;
use Illuminate\Support\Collection;

class BillingInfoService
{
    public function massStore(Collection $storedItemInfos, array $customPrices): Collection
    {
        return $storedItemInfos->map(function (StoredItemInfo $info, $key) use ($customPrices) {
            return $info->getBillingInfo($customPrices[$key]);
        })->pipe(function (Collection $billingInfos) {
            $massWriter = new BillingInfosMassWriter($billingInfos->all());
            return collect($massWriter->write());
        });
    }
}
