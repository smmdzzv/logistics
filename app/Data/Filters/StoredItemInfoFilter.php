<?php


namespace App\Data\Filters;


use App\Models\Branch;
use App\Models\StoredItems\StoredItem;
use App\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class StoredItemInfoFilter extends Filter
{
    public function filter()
    {
        $filters = $this->filters;
        $ownerId = isset($this->filters['client']) ? User::where('code', $this->filters['client'])->firstOrFail()->id : null;
        $this->applyOwnerScope('ownerId', $ownerId);
        $this->applyStoredItemsFilters();

        if (isset($filters['item']))
            $this->query->whereHas('item', function (Builder $query) {
                $query->where('id', $this->filters['item']);
            });

        if (isset($filters['minCubage']))
            $this->query->whereRaw('(height * width * length) >= ' . $filters['minCubage']);

        if (isset($filters['maxCubage']))
            $this->query->whereRaw('(height * width * length) <= ' . $filters['maxCubage']);

        if (isset($filters['minWeight']))
            $this->query->where('weight', '>=', $filters['minWeight']);

        if (isset($filters['maxWeight']))
            $this->query->where('weight', '<=', $filters['maxWeight']);

        if (isset($filters['minPrice']))
            $this->query->whereHas('billingInfo', function (Builder $query) {
                $query->whereRaw('(totalPrice / count) >=' . $this->filters['minPrice']);
            });

        if (isset($filters['maxPrice']))
            $this->query->whereHas('billingInfo', function (Builder $query) {
                $query->whereRaw('(totalPrice / count) <=' . $this->filters['maxPrice']);
            });

        if (isset($filters['dateFrom']))
            $this->query->where('created_at', '>=', $filters['dateFrom']);

        if (isset($filters['dateTo']))
            $this->query->where('created_at', '<=', Carbon::createFromDate($filters['dateTo'])->addDay());

        return $this->query;
    }


    private function applyStoredItemsFilters()
    {
        $branch = null;
        if (!auth()->user()->hasAnyRole(['admin', 'manager']))
            $branch = auth()->user()->branch;
        else if (request('branch'))
            $branch = Branch::find(request('branch'));

        $this->query->with(['storedItems.storageHistory.storage.branch', 'storedItems' => function ($query) use ($branch) {
            $query->withTrashed();

            if (isset($this->filters['trip'])) {
                $query = $this->applyTripScope($query);
            }

            if ($branch) {
                $query->whereHas('storage', function (Builder $query) use ($branch) {
                    $query->where('branch_id', $branch->id)->where('deleted_at', null);
                });
            }

            if(isset($this->filters['status']))
                $query->where('status', $this->filters['status']);

//            if (isset($this->filters['status']) && $this->filters['status'] === 'completed') {
//                $query->onlyTrashed();
//            } elseif (!isset($this->filters['status']))

        }]);
    }

    private function applyTripScope($query)
    {
        switch ($this->filters['trip']) {
            case 'hasTrip':
                return $query->whereHas('tripHistory');
            case 'doesntHaveTrip':
                return $query->whereDoesntHave('tripHistory');
            default:
                return $query;
        }
    }
}
