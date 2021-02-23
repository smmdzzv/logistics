<?php


namespace App\Data\MassWriters\Order;


use App\Data\MassWriters\MassWriter;
use App\Models\StoredItems\StorageHistory;

class StorageHistoriesWriter extends MassWriter
{
    public function __construct(array $entities)
    {
        parent::__construct($entities);
        $this->entityClass = StorageHistory::class;
    }
}
