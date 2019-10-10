<?php


namespace App\Data\MassWriters\Trip;


use App\Data\MassWriters\MassWriter;
use App\Models\StoredItems\StoredItemTripHistory;

class StoredItemTripHistoryWriter extends MassWriter
{
    public function __construct(array $entities)
    {
        parent::__construct($entities);
        $this->entityClass = StoredItemTripHistory::class;
    }
}
