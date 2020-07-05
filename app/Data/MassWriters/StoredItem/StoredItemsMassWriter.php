<?php


namespace App\Data\MassWriters\StoredItem;


use App\Data\MassWriters\MassWriter;
use App\Models\StoredItems\StoredItem;

class StoredItemsMassWriter extends MassWriter
{
    public function __construct(array $entities)
    {
        parent::__construct($entities);
        $this->entityClass = StoredItem::class;
    }
}
