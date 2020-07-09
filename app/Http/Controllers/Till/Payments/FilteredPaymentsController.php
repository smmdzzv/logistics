<?php
/**
 *
 * @author Sultonazar Mamadazizov <sultonazar.mamadazizov@mail.ru> on 07.07.2020
 */

namespace App\Http\Controllers\Till\Payments;


use App\Data\Filters\PaymentFilter;
use App\Data\Reports\CashReport;
use App\Http\Controllers\BaseController;
use App\Http\Controllers\Controller;
use App\Models\Till\Payment;

class FilteredPaymentsController extends BaseController
{
    public function index()
    {

        $query = Payment::without(
            'payerAccount',
            'payeeAccount',
            'exchangeRate'
        )->latest();

        $filter = new PaymentFilter(request()->all(), $query);
        $query = $filter->filter();

        $report = null;

        if (request()->get('calculateCash') === 'true'
            && request()->get('branchPayer')) {
            $report = new CashReport($query, request()->get('branchPayer'));
            $report->formReport();
            $report->convertToISONameKey();
        }

        $paginator = $query->paginate($this->pagination());
        $paginator['cashReport'] = $report ? $report->toArray() : null;
        return $paginator;
    }
}
