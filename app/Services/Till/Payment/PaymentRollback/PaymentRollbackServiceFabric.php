<?php

namespace App\Services\Till\Payment\PaymentRollback;

use App\Data\Dto\Payment\PaymentAccountsDto;
use App\Models\Till\Payment;
use App\Services\Till\Account\AccountService;
use App\Services\Till\Payment\PaymentAccount\BalanceReplenishmentPaymentAccountsService;
use App\Services\Till\Payment\PaymentAccount\BonusPaymentAccountsService;
use App\Services\Till\Payment\PaymentAccount\DefaultPaymentAccountsService;
use App\Services\Till\Payment\PaymentAccount\TransferBetweenBranchAccountsService;

class PaymentRollbackServiceFabric
{

    private Payment $payment;

    private AccountService $accountService;

    public function __construct(Payment $payment)
    {
        $this->payment = $payment;
        $this->accountService = new AccountService();
    }

    public function getWriter()
    {
        switch ($this->payment->paymentItem->title) {
            case 'Пополнение баланса':
                return new BalanceReplenishmentPaymentRollback(
                    $this->payment,
                    $this->getPaymentAccountsDto(),
                    $this->accountService
                );
            case 'Перевод между счетами филиала':
                return new TransferBetweenBranchAccountsPaymentRollback(
                    $this->payment,
                    $this->getPaymentAccountsDto(),
                    $this->accountService
                );
            case 'Обмен валют':
                return new MoneyExchangePaymentRollback(
                    $this->payment,
                    $this->getPaymentAccountsDto(),
                    $this->accountService
                );
            default:
                return new DefaultPaymentRollback(
                    $this->payment,
                    $this->getPaymentAccountsDto(),
                    $this->accountService
                );
        }
    }

    public function getPaymentAccountsDto(): PaymentAccountsDto
    {
        $service = null;

        switch ($this->payment->paymentItem->title) {
            case 'Пополнение баланса':
                $service = new BalanceReplenishmentPaymentAccountsService($this->payment);
                break;
            case 'Перевод между счетами филиала':
                $service = new TransferBetweenBranchAccountsService($this->payment);
                break;
            case 'Бонус':
                $service= new BonusPaymentAccountsService($this->payment);
                break;
            default:
                $service = new DefaultPaymentAccountsService($this->payment);
                break;
        }

        return $service->getPaymentAccounts();
    }
}
