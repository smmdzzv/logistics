<?php
/**
 * @author Sultonazar Mamadazizov <sultonazar.mamadazizov@mail.ru>
 */

namespace App\Data\Dto\Actions;


use App\Models\StoredItems\StoredItem;
use App\Models\Trip;
use Illuminate\Support\Collection;
use Spatie\DataTransferObject\FlexibleDataTransferObject;

class CarToCarDto extends FlexibleDataTransferObject
{
    public String $tripId;

    public String $targetTripId;

    public Collection $storedItems;

    public function __construct(array $parameters = [])
    {
        $parameters['storedItems'] = StoredItem::whereIn('id', $parameters['storedItems'])->get();

        parent::__construct($parameters);
    }
}
