<?php


namespace App\Data\Filters;


use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class PaymentFilter extends Filter
{
    function filter()
    {
        $filters = $this->filters;

        if (!auth()->user()->hasRole('admin'))
            $this->query->where('payer_id', auth()->user()->branch->id)
                ->orWhere('payee_id', auth()->user()->branch->id);
        else {
            if (isset($filters['userPayer']))
                $this->query->where('payer_id', $filters['userPayer']);

            if (isset($filters['userPayee']))
                $this->query->where('payee_id', $filters['userPayee']);

            if (isset($filters['branchPayer']))
                $this->query->orWhere('payer_id', $filters['branchPayer']);

            if (isset($filters['branchPayee']))
                $this->query->orWhere('payee_id', $filters['branchPayee']);
        }

        if (isset($filters['item']))
            $this->query->whereHas('paymentItem', function (Builder $query) use ($filters) {
                $query->where('id', $filters['item']);
            });

        if (isset($filters['withTrashed']))
            $this->query->withTrashed();

        if (isset($filters['from']))
            $this->query->where('created_at', '>', Carbon::createFromDate($filters['from']));

        if (isset($filters['to']))
            $this->query->where('created_at', '<', Carbon::createFromDate($filters['to'])->addDay());

        if (isset($filters['cashier']))
            $this->query->whereHas('editor', function (Builder $query) use ($filters) {
                $query->where('id', $filters['cashier']);
            });

        if (isset($filters['minPaidAmount']))
            $this->query->where('paidAmountInBillCurrency', '>=', $filters['minPaidAmount'])
                ->where('paidAmountInSecondCurrency', '>=', $filters['minPaidAmount']);

        if (isset($filters['maxPaidAmount']))
            $this->query->where('paidAmountInBillCurrency', '<=', $filters['maxPaidAmount'])
                ->where('paidAmountInSecondCurrency', '<=', $filters['maxPaidAmount']);

        if (isset($filters['paidCurrency']))
//            $this->query->whereHas('paidCurrency', function (Builder $query) use ($filters) {
//                $query->where('id', $filters['paidCurrency']);
//            });
            $this->query->where('bill_currency_id', $filters['paidCurrency'])
                ->orWhere('second_paid_currency_id', $filters['paidCurrency']);

        if (isset($filters['status']))
            $this->query->where('status', $filters['status']);

        return $this->query;
    }
}
