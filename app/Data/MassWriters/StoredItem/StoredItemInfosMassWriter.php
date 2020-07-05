<?php


namespace App\Data\MassWriters\StoredItem;


use App\Data\MassWriters\MassWriter;
use App\Models\StoredItems\StoredItemInfo;

class StoredItemInfosMassWriter extends MassWriter
{
    public function __construct(array $entities)
    {
        parent::__construct($entities);
        $this->entityClass = StoredItemInfo::class;
    }
}
