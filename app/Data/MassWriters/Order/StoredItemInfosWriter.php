<?php


namespace App\Data\MassWriters\Order;


use App\Data\MassWriters\MassWriter;
use App\Models\StoredItems\StoredItemInfo;

class StoredItemInfosWriter extends MassWriter
{
    public function __construct(array $entities)
    {
        parent::__construct($entities);
        $this->entityClass = StoredItemInfo::class;
    }
}
