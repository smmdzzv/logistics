<?php
/**
 *
 * @author Sultonazar Mamadazizov <sultonazar.mamadazizov@mail.ru> on 07.07.2020
 */

namespace App\Http\Controllers\Till\Payments;


use App\Data\Filters\PaymentFilter;
use App\Http\Controllers\Controller;
use App\Models\Till\Payment;

class FilteredPayments extends Controller
{
    public function index()
    {
        $query = Payment::with('accountTo', 'cashier', 'payer', 'currency', 'paymentItem')
            ->latest();

        $filter = new PaymentFilter(request()->all(), $query);
        $query = $filter->filter();

        return $query->paginate(20);
    }
}
