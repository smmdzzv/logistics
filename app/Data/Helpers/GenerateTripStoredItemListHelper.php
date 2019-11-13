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

    /**
     * Available stored items
     * @var Collection<StoredItem> $storedItems
     */
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
        });

        $this->storedItems = $this->storedItems
//            ->groupBy(['info.ownerId', 'info.id'])
//            ->flatten(2)
            ->sortByDesc('info.weight')
            ->values();
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

    /**
     * @return Collection<StoredItem>
     */
    public function generate()
    {
        $options = $this->calculatePossibleOptions();

        return $this->storedItems->filter(function ($item) use ($options) {
            return $options->first(function ($id) use ($item) {
                return $item->id === $id;
            });
        });
    }

    /**
     * @return Collection
     */
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
            $this->normalizeWeight($result, $this->removeItems($result, $this->storedItems));


        $result = $result->map(function ($item) {
            return $item->id;
        });

        return $result;
    }

    /**
     * @param Collection $removeArr
     * @param Collection $originArr
     * @return Collection
     */
    private function removeItems($removeArr, $originArr)
    {
        return $originArr->filter(function ($item) use ($removeArr) {
            $found = $removeArr->first(function ($result) use ($item) {
                return $result->id === $item->id;
            });

            return $found === null;
        });
    }

    /**
     * @param Collection $origin
     * @param Collection $options
     * @return Collection
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
            $targetWeight = $maxWeight + $selected->info->weight;

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

        $normalizedResult = $this->removeItems($removeItems, $origin);
        $normalizedResult->merge($addItems);

        return $normalizedResult;
    }

}
