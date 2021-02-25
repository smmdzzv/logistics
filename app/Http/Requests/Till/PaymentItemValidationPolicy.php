<?php
/**
 * @author Sultonazar Mamadazizov <sultonazar.mamadazizov@mail.ru>
 */

namespace App\Http\Requests\Till;


use App\Models\Branch;
use App\Models\Currency;
use App\Models\Till\PaymentItem;
use App\User;

class PaymentItemValidationPolicy extends PaymentValidationPolicy
{
    public function apply()
    {
        $paymentItem = PaymentItem::find($this->request->get('paymentItem'));

        if ($this->payer instanceof Branch && $this->payee instanceof Branch
            && $paymentItem->title !== 'Перевод между счетами филиала' && $paymentItem->title !== 'Перевод между филиалами')
            return $this->validator->errors()->add('payer', 'Перевод денег между филиалами возможен для статей "Перевод между филиалами" или "Перевод между счетами филиала". ');

        $this->validatePaymentItems($paymentItem, $this->validator);

        $this->validationPolicy->apply();
    }

    private function validatePaymentItems(PaymentItem $paymentItem, $validator)
    {
        switch ($paymentItem->title) {
            case 'Пополнение баланса':
                $this->validateBalanceReplenishment($validator);
                break;
            case 'Перевод между филиалами':
                $this->validateTransferBetweenBranches($validator);
                break;
            case 'Перевод между счетами филиала':
                $this->validateTransferBetweenBranchAccounts($validator);
                break;
            case 'Прием наличных средств':
                $this->validateCashAccepting($validator);
                break;
            case 'Аванс':
            case 'Зарплата':
            case 'Выдача наличных средств':
                $this->validateCashWithdrawal($validator);
                break;
            case 'Обмен валют':
                $this->validateMoneyExchange($validator);
                break;
        }
    }

    private function validateBalanceReplenishment($validator)
    {
        if (!($this->payer instanceof User))
            return $validator->errors()->add('payer', 'При пополнении баланса плательщиком должен являться клиент. ');

        if (Currency::find($this->request->get('billCurrency'))->isoName !== 'USD')
            return $validator->errors()->add('billCurrency', 'При пополнении баланса счет должен выставляться в долларах. ');
    }

    private function validateTransferBetweenBranches($validator)
    {
        if (!($this->payer instanceof Branch))
            return $validator->errors()->add('payer', 'При переводе денег между филиалами плательщиком должен быть филиал. ');

        if (!($this->payee instanceof Branch))
            return $validator->errors()->add('payee', 'При переводе денег между филиалами получателем должен быть филиал. ');

        if ($this->payer->id === $this->payee->id)
            return $validator->errors()->add('payee', 'При переводе денег между филиалами получатель и плательщик должны отличаться. ');
    }

    private function validateTransferBetweenBranchAccounts($validator)
    {
        if ($this->payer->id !== $this->payee->id)
            return $validator->errors()->add('payer', 'При переводе денег между счетами филиала плательщиком и получателем должен быть один филиал. ');

        if ($this->request->get('billCurrency') === $this->request->get('secondPaidCurrency'))
            return $validator->errors()->add('billCurrency', 'При переводе денег между счетами филиала валюта оплаты и валюта зачисления не должны совпадать. ');

        if ($this->request->get('paidAmountInBillCurrency') > 0)
            return $validator->errors()->add('paidAmountInBillCurrency', 'При переводе денег между счетами филиала сумма в валюте оплаты должны быть равна 0. ');
    }

    private function validateCashAccepting($validator)
    {
        if (!($this->payer instanceof User))
            return $validator->errors()->add('payer', 'При приеме наличных средств плательщиком должен быть указан пользователь. ');

        if (!($this->payee instanceof Branch))
            return $validator->errors()->add('payee', 'При приеме наличных средств получателем должен быть указана филиал. ');
    }

    private function validateCashWithdrawal($validator)
    {
        if (!($this->payer instanceof Branch))
            return $validator->errors()->add('payer', 'При выдаче наличных средств плательщиком должен быть указан филиал. ');

        if (!($this->payee instanceof User))
            return $validator->errors()->add('payee', 'При выдаче наличных средств получателем должен быть указана пользователь. ');
    }

    private function validateMoneyExchange($validator)
    {
        if (!($this->payee instanceof Branch))
            return $validator->errors()->add('payee', 'При обмене валют получателем должен быть филиал, а плательщиком клиент. ');

        if (!($this->payer instanceof User))
            return $validator->errors()->add('payer', 'При обмене валют плательщиком должен быть клиент, а получателем филиал. ');

        if ($this->request->get('billCurrency') === $this->request->get('secondPaidCurrency'))
            return $validator->errors()->add('secondPaidCurrency', 'При обмене валют требуемая валюта должна отличаться от валюты оплаты.');

        if (!$this->request->get('exchangeRate'))
            return $validator->errors()->add('exchangeRate', 'Курс обмена не указан. ');

        if ($this->request->get('paidAmountInBillCurrency') > 0)
            return $validator->errors()->add('paidAmountInBillCurrency', 'Сумма в целевой валюте должна равняться нулю. ');

        //check branch payment account
        $payeeAccount = $this->payee->accounts()->where('currency_id', $this->request->get('billCurrency'))->firstOrFail();

        if ($payeeAccount->balance - $this->request->get('billAmount') < 0)
            return $validator->errors()->add('payee',
                    'Недостаточно средств на счету филиала для выдачи валюты - '
                    . $payeeAccount->balance
                    . ' ' . $payeeAccount->currency->isoName) . '. ';

    }
}
