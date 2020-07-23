<?php


namespace App\Data\Filters;


use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class PaymentFilter extends Filter
{
    function filter()
    {
        $filters = $this->filters;

        $this->filterByPayerAndPayee();

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


        $this->filterByBranch();

        return $this->query;
    }

    private function filterByBranch()
    {
        if (isset($this->filters['branch'])) {
            $this->query->where('branch_id',
                auth()->user()->hasRole('admin') ? $this->filters['branch'] : auth()->user()->branch->id
            );
        }
    }

    private function filterByPayerAndPayee()
    {
        if (!auth()->user()->hasRole('admin')) {
            $branchId = auth()->user()->branch->id;

            if (!isset($this->filters['userPayer']) && !isset($this->filters['userPayee']))
                $this->query->where('payer_id', $branchId)->orWhere('payee_id', $branchId);

            if (isset($this->filters['userPayer'])) {
                $this->query->where('payer_id', $this->filters['userPayer'])
                    ->where('payer_type', 'user')
                    ->where('payee_id', $branchId);
            }

            if (isset($this->filters['userPayee'])) {
                $this->query->where('payee_id', $this->filters['userPayee'])
                    ->where('payee_type', 'user')
                    ->where('payer_id', $branchId);
            }

        } else {
            if (isset($this->filters['userPayer']))
                $this->query->where('payer_id', $this->filters['userPayer']);

            if (isset($this->filters['userPayee']))
                $this->query->where('payee_id', $this->filters['userPayee']);

            if (isset($this->filters['branchPayer']))
                $this->query->where('payer_id', $this->filters['branchPayer']);

            if (isset($this->filters['branchPayee']))
                $this->query->where('payee_id', $this->filters['branchPayee']);
        }
    }
}
