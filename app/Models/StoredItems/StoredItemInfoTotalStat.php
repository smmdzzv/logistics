<?php


namespace App\Models\StoredItems;


use App\Models\SerializableModel;

class StoredItemInfoTotalStat extends SerializableModel
{
    public float $totalPrice = 0;
    public float $totalCubage = 0;
    public float $totalWeight = 0;
    public int $totalPlacesCount = 0;
    public int $averageWeightPerCube = 0;
    public float $weightPerCubeSum = 0;

    public function toArray()
    {
        return [
            'totalPrice' => $this->totalPrice,
            'totalCubage' => $this->totalCubage,
            'totalWeight' => $this->totalWeight,
            'totalPlacesCount' => $this->totalPlacesCount,
            'averageWeightPerCube' => $this->averageWeightPerCube,
            'weightPerCubeSum' => $this->weightPerCubeSum,
        ];
    }
}
