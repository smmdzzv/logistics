<?php


namespace App\Data\Filters;


use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class OrderFilter extends Filter
{

    /**
     * @return Builder $query
     */
    function filter()
    {
        $filters = $this->filters;

        if (isset($filters['branch']) && auth()->user()->hasRole('admin'))
            $this->query->where('branch_id', $filters['branch']);

        if (isset($filters['client']))
            $this->query->whereHas('owner', function ($query) use ($filters) {
                $query->where('code', $filters['client']);
            });

        if (isset($filters['employee']))
            $this->query->whereHas('registeredBy', function ($query) use ($filters) {
                $query->where('code', $filters['employee']);
            });

        if (isset($filters['minCubage']))
            $this->query->where('totalCubage', '>=', $filters['minCubage']);

        if (isset($filters['maxCubage']))
            $this->query->where('totalCubage', '<=', $filters['maxCubage']);

        if (isset($filters['minWeight']))
            $this->query->where('totalWeight', '>=', $filters['minWeight']);

        if (isset($filters['maxWeight']))
            $this->query->where('totalWeight', '<=', $filters['maxWeight']);

        if (isset($filters['minPrice']))
            $this->query->where('totalPrice', '>=', $filters['minPrice']);

        if (isset($filters['maxPrice']))
            $this->query->where('totalPrice', '<=', $filters['maxPrice']);

        if (isset($filters['dateFrom']))
            $this->query->where('created_at', '>=', $filters['dateFrom']);

        if (isset($filters['dateTo']))
            $this->query->where('created_at', '<=', Carbon::createFromDate($filters['dateTo'])->addDay());

        if(isset($filters['status']))
            $this->query->where('status', $filters['status']);

        return $this->query;
    }
}
