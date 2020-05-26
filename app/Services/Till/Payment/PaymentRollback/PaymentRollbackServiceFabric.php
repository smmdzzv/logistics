<?php

namespace App\Services\Till\Payment\PaymentRollback;

use App\Data\Dto\Payment\PaymentAccountsDto;
use App\Models\Till\Payment;
use App\Services\Till\Account\AccountService;
use App\Services\Till\Payment\PaymentAccount\DefaultPaymentAccountsService;
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
//            case 'Пополнение баланса':
//                $this->writer = new BalanceReplenishmentPaymentRequestWriter($this->request);
//                break;
//            case 'Перевод между счетами филиала':
//                $this->writer = new TransferBetweenBranchAccountsRequestWriter($this->request);
//                break;
//            case 'Обмен валют':
//                $this->writer = new MoneyExchangePaymentRequestWriter($this->request);
//                break;
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
//            case 'Пополнение баланса':
//                $this->writer = new BalanceReplenishmentPaymentRequestWriter($this->request);
//                break;
//            case 'Перевод между счетами филиала':
//                $this->writer = new TransferBetweenBranchAccountsRequestWriter($this->request);
//                break;
//            case 'Обмен валют':
//                $this->writer = new MoneyExchangePaymentRequestWriter($this->request);
//                break;
            default:
                $service = new DefaultPaymentAccountsService($this->payment);
                break;
        }

        return $service->getPaymentAccounts();
    }
}
