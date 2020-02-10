<?php


namespace App\Data\Filters;


use Illuminate\Database\Eloquent\Builder;

class StoredItemInfoFilter extends Filter
{
    public function filter()
    {
        $filters = $this->filters;

        if (isset($filters['item']))
            $this->query->whereHas('paymentItem', function (Builder $query) use ($filters) {
                $query->where('id', $filters['item']);
            });
    }
}