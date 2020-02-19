<?php


namespace App\Data\Filters;


use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class PaymentFilter extends Filter
{
    function filter()
    {
        $filters = $this->filters;

        if (isset($filters['item']))
            $this->query->whereHas('paymentItem', function (Builder $query) use ($filters) {
                $query->where('id', $filters['item']);
            });

        if (isset($filters['branch']))
            $this->query->whereHas('branch', function (Builder $query) use ($filters) {
                $query->where('id', $filters['branch']);
            });

//        if (isset($filters['user']))
//            $this->query->whereHas('payer', function (Builder $query) use ($filters) {
//                $query->where('id', $filters['user']);
//            });

        if (isset($filters['from']))
            $this->query->where('created_at', '>', Carbon::createFromDate($filters['from']));

        if (isset($filters['to']))
            $this->query->where('created_at', '<', Carbon::createFromDate($filters['to'])->addDay());

        if (isset($filters['cashier']))
            $this->query->whereHas('cashier', function (Builder $query) use ($filters) {
                $query->where('id', $filters['cashier']);
            });

        if (isset($filters['minPaidAmount']))
            $this->query->where('paidAmount', '>=', $filters['minPaidAmount']);

        if (isset($filters['maxPaidAmount']))
            $this->query->where('paidAmount', '<=', $filters['maxPaidAmount']);

        if (isset($filters['paidCurrency']))
            $this->query->whereHas('paidCurrency', function (Builder $query) use ($filters) {
                $query->where('id', $filters['paidCurrency']);
            });

        if (isset($filters['selectedStatus']))
            $this->query->where('status', $filters['selectedStatus']);

        return $this->query;
    }
}
