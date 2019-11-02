<?php


namespace App\Data\Filters;


use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class PaymentFilter extends Filter
{
    public function __construct($filters, $query)
    {
        parent::__construct($filters, $query);
    }

    function filter()
    {
        $filters = $this->filters;

        if (isset($filters['item']))
            $this->query->whereHas('paymentItem', function (Builder $query) use($filters) {
                $query->where('id', $filters['item']);
            });
        else if (isset($filters['type']))
            $this->query->whereHas('paymentItem', function (Builder $query) use ($filters) {
                $query->where('type', $filters['type']);
            });

        if (isset($filters['branch']))
            $this->query->whereHas('branch', function (Builder $query) use ($filters) {
                $query->where('id', $filters['branch']);
            });

        if (isset($filters['user']))
            $this->query->whereHas('payer', function (Builder $query) use ($filters) {
                $query->where('id', $filters['user']);
            });

        if (isset($filters['currency']))
            $this->query->whereHas('currency', function (Builder $query) use ($filters) {
                $query->where('id', $filters['currency']);
            });

        if (isset($filters['from']))
            $this->query->where('created_at', '>', Carbon::createFromDate($filters['from']));

        if (isset($filters['to']))
            $this->query->where('created_at', '<', Carbon::createFromDate($filters['to'])->addDay());

        if (isset($filters['cashier']))
            $this->query->whereHas('cashier', function (Builder $query) use ($filters) {
                $query->where('id', $filters['cashier']);
            });

        if (isset($filters['min']))
            $this->query->where('amount', '>=', $filters['min']);

        if (isset($filters['max']))
            $this->query->where('amount', '<=', $filters['max']);

        return $this->query;
    }
}
