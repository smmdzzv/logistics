<?php


namespace App\Data\Helpers;


use App\Models\StoredItems\StoredItem;
use App\Models\Trip;
use Illuminate\Support\Collection;

class GenerateTripStoredItemListHelper
{
    /**
     * Trip for which item list will be generated
     * @var Trip $trip
     */
    private $trip;

    private $storedItems;

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

        $this->setMaxValues();

        $this->considerTripStoredItems();
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
            ->sortByDesc('info.weight')
            ->values();
//        dd($this->storedItems->toArray());
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

    /**
     *Sets initial values of $cubage and $weight if Trip already has stored items
     */
    private function considerTripStoredItems()
    {
        $this->trip->storedItems->each(function ($item) {
            $this->maxCubage -= $item->info->length * $item->info->height * $item->info->width;

            $this->maxWeight -= $item->info->weight;
        });

        if ($this->maxWeight < 0)
            abort(403, 'Общий вес товаров рейса превышает грузоподъемность машины');

        if ($this->maxCubage < 0)
            abort(403, 'Общий объем товаров рейса превышает вместимость машины');

    }

    public function generate()
    {
//        dd($this->storedItems->toArray());
        $options = $this->calculatePossibleOptions();
        return $options;
//        $maxWeights = $this->extractOptionsMaxWeights($options);

//        $index = count($options);

//        $optimalTripItemsArray = [];
//
//        while ($index) {
//
//            if (count($options[--$index]) > 1) {
//
//                $optimalTripItemsArray = array_map(function ($item) {
//
//                    return isset($item['id']) ? $item['id'] : null;
//
//                }, $options[--$index]);
//
//                break;
//            }
//
//        }
//
//        $optimalTripItemsArray = array_filter($optimalTripItemsArray, function ($item) {
//            return $item !== null;
//        });

//        return $optimalTripItemsArray;
    }

//    private function extractOptionsMaxWeights($options){
//        $maxArr = [];
//
//        foreach ($options as $item) {
//            $maxArr[] = max($item);
//        }
//
//        return $maxArr;
//    }

    private function calculatePossibleOptions()
    {
//        $n = count($this->storedItems);

        $result = new Collection();

        $maxC = $this->maxCubage;
        $maxW = $this->maxWeight;


        foreach ($this->storedItems as $stored) {
            $cubage = $stored->info->cubage;
            $weight = $stored->info->weight;

            if ($maxW - $weight > 0) {
                if ($maxC - $cubage > 0) {
                    $result->push($stored);
                    $maxC -= $cubage;
                    $maxW -= $weight;
                }
            }
        }


        if ($maxW > 50)
            $this->normalizeWeight($result, $this->filterItems($result));


        $result = $result->map(function ($item) {
            return $item->id;
        });

        return $result->toArray();
    }

    private function filterItems($results)
    {
        return $this->storedItems->filter(function ($item) use ($results) {
            $found = $results->first(function ($result) use ($item) {
                return $result->id === $item->id;
            });

            return $found === null;
        });
    }

    /**
     * @param Collection $origin
     * @param Collection $options
     */
    private function normalizeWeight($origin, $options)
    {
        $origin = $origin->sortBy('info.cubage');
        $options = $options->sortBy('info.cubage');

        $originWeight = $origin->sum('info.weight');

        $maxWeight = $this->maxWeight - $originWeight;

        $removeItems = new Collection();
        $addItems = new Collection();

        foreach ($origin as $selected) {
            $targetCubage = $selected->info->cubage;
            $targetWeight = $maxWeight;

            $filteredOptions = $options->filter(function ($option) use ($targetCubage) {
                return $option->info->cubage <= $targetCubage;
            })->sortByDesc('info.weight');

            $substituteItems = new Collection();

            foreach ($filteredOptions as $option) {
                if ($targetCubage - $option->info->cubbage > 0
                    && $targetWeight - $option->info->weight > 0) {
                    $substituteItems->push($option);
                    $targetCubage -= $option->info->cubage;
                    $targetWeight -= $option->info->weight;
                }
            }

            $substituteItemsWeight = $substituteItems->sum('info.weight');

            if ($substituteItems->count() > 0 && $substituteItemsWeight > $selected->info->weight) {
                $removeItems->push($selected);
                $addItems->push($substituteItems);
                $maxWeight -= $substituteItemsWeight;
            }
        }


    }


//    private function normalizeCubage($origin, $options)
//    {
//        $options = $options->map(function ($item) {
//            $item->specificVolume = $item->info->cubage / $item->info->weight;
//        })->sortBy('specificVolume');
//
////        $currentCubage = $origin->count('info.cubage');
//        $currentCubage = 0;
////dd(count($origin));
//        foreach ($origin as $item) {
//            $currentCubage += $item->info->cubage;
//        }
//        dd($currentCubage);
//
////        while($this->maxCubage - )
//
////        for ($i = 0; $i < count($origin); $i++){
////
////            while($targetCubage - )
////        }
//    }

    /**
     * @return array
     */
//    private function calculatePossibleOptions2()
//    {
//
//        $n = count($this->storedItems);
//        $C = $this->maxCubage;
//        $d = [0 => array_fill(0, $C + 1, ['weight' => 0])];
//
//        for ($i = 1; $i <= $n; $i++) {
//            $achieved = false;
//
//            for ($j = 0; $j <= $C; $j++) {
//
//                if ($j > 0 && isset($d[$i - 1][$j]['id']) && isset($d[$i - 1][$j - 1]['id'])
//                    && $d[$i - 1][$j - 1]['id'] === $d[$i - 1][$j]['id']
//                    && $d[$i][$j - 1]['id'] === $this->storedItems[$i - 1]->id) {
//                    $d[$i][$j] = $d[$i][$j - 1];
//                    continue;
//                }
//
//
//                $d[$i][$j] = $d[$i - 1][$j];
//
//                $wi = $this->storedItems[$i - 1]->info->weight;
//                $ci = $this->storedItems[$i - 1]->info->cubage;
//
//
//                if ($j - $ci >= 0 && $d[$i][$j]['weight'] < $d[$i - 1][$j - $ci]['weight'] + $wi) {
//
//                    $newWeight = $d[$i - 1][$j - $ci]['weight'] + $wi;
//
//                    if ($newWeight > $this->maxWeight) {
//                        array_splice($d[$i], $j, 1);
//                        $achieved = true;
//                        break;
//                    }
//
//                    $d[$i][$j]['weight'] = $newWeight;
//                    $d[$i][$j]['id'] = $this->storedItems[$i - 1]->id;
//                }
//            }
//            if ($achieved)
//                break;
//        }
////        dd($d[100]);
//        return $d;
////        $max = [];
////
////        foreach ($d as $item) {
////            $max[] = max($item);
////        }
////
////
////        dd($max, $d[count($d) - 1], $n, $this->maxCubage);
//    }

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
