<?php

namespace App\Services\Till\Account;

use App\Models\Till\Account;

class AccountService
{
    public function withdrawMoney(Account $account, float $amount)
    {
        return $account->decrement('balance', $amount);
    }

    public function putMoney(Account $account, float $amount)
    {
        return $account->increment('balance', $amount);
    }
}
