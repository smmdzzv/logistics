<?php

namespace App\Data\Dto\Payment;

use App\Models\Till\Account;

class PaymentAccountsDto
{
    public ?Account $payerAccountInBillCurrency = null;

    public ?Account $payeeAccountInBillCurrency = null;

    public ?Account $payerAccountInSecondCurrency = null;

    public ?Account $payeeAccountInSecondCurrency = null;
}
