<?php


namespace App\Data\Filters;


use Illuminate\Database\Eloquent\Builder;

abstract class Filter
{
    protected $filters;

    protected $query;

    protected function __construct($filters, $query)
    {
        $this->filters = $filters;
        $this->query = $query;
    }

    abstract function filter();
}
