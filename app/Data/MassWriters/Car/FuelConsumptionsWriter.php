<?php


namespace App\Data\MassWriters\Car;


use App\Data\MassWriters\MassWriter;
use App\Models\FuelConsumption;

class FuelConsumptionsWriter extends MassWriter
{
    public function __construct(array $entities)
    {
        parent::__construct($entities);
        $this->entityClass = FuelConsumption::class;
    }
}
