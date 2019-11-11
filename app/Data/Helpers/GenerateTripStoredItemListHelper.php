<?php


namespace App\Data\Helpers;


use App\Models\StoredItems\StoredItem;
use App\Models\Trip;

class GenerateTripStoredItemListHelper
{
    /**
     * Trip for which item list will be generated
     * @var Trip $trip
     */
    private $trip;

    private $storedItems;

    private $cubage;

    private $weight;

    private $maxCubage;

    private $maxWeight;

    /**
     * GenerateTripStoredItemListHelper constructor.
     * @param Trip $trip
     * @param array<StoredItem> $storedItems
     */
    public function __construct($trip, $storedItems)
    {
        $this->trip = $trip;
        $this->storedItems = $storedItems;

        $this->prepareStoredItems();

        $this->setInitialValues();

        $this->setMaxValues();
    }

    /**
     *Prepares data for list generation. Dynamically adds cubage and density properties
     *to each storedItem.info and storedItem correspondingly with further sorting.
     */
    private function prepareStoredItems()
    {
        $this->storedItems->each(function ($item) {
            $item->info->cubage = $item->info->length * $item->info->height * $item->info->width;

//            $item->specificVolume = $item->info->cubage / $item->info->weight;
        });

        $this->storedItems = $this->storedItems
            ->groupBy(['info.ownerId', 'info.id'])
            ->flatten(2)
            ->sortBy('info.weight')
            ->values();
//        dd($this->storedItems->toArray());
    }

    /**
     *Sets initial values of $cubage and $weight if Trip already has stored items
     */
    private function setInitialValues()
    {
        $this->trip->storedItems->each(function ($item) {
            $this->cubage += $item->info->length * $item->info->height * $item->info->width;

            $this->weight += $item->info->weight;
        });
    }

    /**
     *Sets maximum values for cubage and weight based on trip and car information
     */
    private function setMaxValues()
    {
        $this->maxCubage += $this->trip->car->maxCubage;
        $this->maxWeight += $this->trip->car->maxWeight;

        if ($this->trip->hasTrailer) {
            $this->maxCubage += $this->trip->car->trailerMaxCubage;
            $this->maxWeight += $this->trip->car->trailerMaxWeight;
        }
    }

    public function generate()
    {

        $n = count($this->storedItems);
        $W = $this->maxWeight;
        $C = $this->maxCubage;
//        $d = [0 => array_fill(0, $W + 1, 0)];
        $d = [0 => array_fill(0, $C + 1, 0)];

//        for ($i = 1; $i <= $n; $i++) {
//
//            for ($j = 0; $j <= $W; $j++) {
//                $d[$i][$j] = $d[$i - 1][$j];
//
//                $wi = $this->storedItems[$i - 1]->info->weight;
//                $ci = $this->storedItems[$i - 1]->info->cubage;
//
//                if ($j - $wi >= 0) {
//                    $d[$i][$j] = max([
//                        $d[$i][$j],
//                        $d[$i - 1][$j - $wi] + $ci
//                    ]);
//                }
//            }
//        }


        for ($i = 1; $i <= $n; $i++) {
            $achieved = false;

            for ($j = 0; $j <= $C; $j++) {
                $d[$i][$j] = $d[$i - 1][$j];

                $wi = $this->storedItems[$i - 1]->info->weight;
                $ci = $this->storedItems[$i - 1]->info->cubage;

                if ($j - $ci >= 0) {
                    $optWeight = max([
                        $d[$i][$j],
                        $d[$i - 1][$j - $ci] + $wi
                    ]);

                    if ($optWeight > $this->maxWeight){
                        $achieved = true;
                        break;
                    }

                    $d[$i][$j] = $optWeight;
                }
            }

            if ($achieved)
                break;
        }

        $max = [];

        foreach ($d as $item) {
            $max[] = max($item);
        }


        dd($max, $n, $this->maxCubage);
    }

//    public function generate()
//    {
//        $list = new Collection();
//
//        $cubage = 0;
//        $weight = 0;
//
//        $deltaC = [];
//        $deltaW = [];
//
//        for ($i = 0; $i < count($this->storedItems); $i++) {
//
//            $stored = $this->storedItems[$i];
//
//            $list->push($stored);
//
//            $cubage += $stored->info->cubage;
//            $weight += $stored->info->weight;
//
//            $delta = $this->maxCubage - $cubage;
//
//            $deltaC[] = $delta;
//            $deltaW[] = $this->maxWeight - $weight;
//
//            if ($delta < 0) {
//                $cubage -= $list[0]->info->cubage;
//                $weight -= $list[0]->info->weight;
//                $list->shift();
//            }
//        }
//
//        $left = $this->storedItems->diff($list)->sortBy('info.weight');
//        $list = $list->sortByDesc('info.weight');
//
//        for ($i = 0; $i < count($left); $i++) {
//
//
//            $stored = $left[$i];
//
//            $list[] = $stored;
//
//            $cubage += $stored->info->cubage;
//            $weight += $stored->info->weight;
//
//            $delta = $this->maxCubage - $cubage;
//
//            $delta_w = $this->maxWeight - $weight;
//
//            $deltaC[] = $delta;
//            $deltaW[] = $delta_w;
//
//            if ($delta_w < 0) {
//
//                $index = 0;
//
//                $wPercentage1 = ($this->maxCubage - $cubage - $list->first()->info->cubage) * 100 / $this->maxCubage;
//                $cPercentage1 = ($this->maxWeight - $weight - $list->first()->info->weight) * 100 / $this->maxWeight;
//
//                $wPercentage2 = ($this->maxCubage - $cubage - $list->last()->info->cubage) * 100 / $this->maxCubage;
//                $cPercentage2 = ($this->maxWeight - $weight - $list->last()->info->weight) * 100 / $this->maxWeight;
//
//                if ($wPercentage1 > 0 && $cPercentage1 > 0
//                    && abs($wPercentage1 - $cPercentage1) < abs($wPercentage2 - $cPercentage2))
//                    $index = count($list) - 1;
//
//                $cubage -= $list[$index]->info->cubage;
//                $weight -= $list[$index]->info->weight;
//
//                $list->splice($index, 1);
//            }
//        }
//
//        $totalCubage = 0;
//        $totalWeight = 0;
//        foreach ($list as $item) {
//            $totalCubage += $item->info->cubage;
//            $totalWeight += $item->info->weight;
//        }
//        dd($totalCubage, $totalWeight, count($list), $deltaC, $deltaW);
//
//        return $list;
//    }
}
