<?php
/**
 * @author Sultonazar Mamadazizov <sultonazar.mamadazizov@mail.ru>
 */

namespace App\Http\Controllers\Till\Payments;


use App\Http\Controllers\Controller;
use App\Models\Till\Payment;

class ModeratedPaymentsController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'roles.allow:financier']);
    }

    public function update(Payment $payment): bool
    {
        return $payment->update(['approved' => request()->get('approved')]);
    }
}
