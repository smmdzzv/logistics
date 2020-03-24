<?php


namespace App\Data\Filters;


use Illuminate\Database\Eloquent\Builder;

abstract class Filter
{
    protected array $filters;

    protected Builder $query;

    public function __construct(array $filters, Builder $query)
    {
        $this->filters = $filters;
        $this->query = $query;
    }

    /**
     * @return Builder $query
     */
    abstract function filter();

    protected function applyOwnerScope($columnName, $ownerId)
    {
        if (!auth()->user()->hasAnyRole(['admin', 'manager', 'storekeeper']))
            $this->query->where($columnName, auth()->user()->id);
        elseif ($ownerId)
            $this->query->where($columnName, $ownerId);
    }
}
