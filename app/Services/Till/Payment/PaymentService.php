<?php

namespace App\Services\Till\Payment;

use App\Models\Till\Payment;
use App\Services\Till\Payment\PaymentRollback\PaymentRollbackServiceFabric;

class PaymentService
{
    public function destroy(Payment $payment)
    {
        $rollbackServiceFabric = new PaymentRollbackServiceFabric($payment);
        $rollbackService = $rollbackServiceFabric->getWriter();
        $rollbackService->rollback();
        return;
    }
}
