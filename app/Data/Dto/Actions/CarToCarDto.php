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
    public Trip $trip;

    public Trip $targetTripId;

    public Collection $storedItems;

    public function __construct(array $parameters = [])
    {
        parent::__construct($parameters);

        $this->storedItems = StoredItem::whereIn('id', $parameters['storedItems'])->get();
    }
}
