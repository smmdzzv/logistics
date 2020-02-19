<?php

namespace App\Http\Requests\Till;

use App\Models\Branch;
use App\Models\Currency;
use App\Models\Till\ExchangeRate;
use App\Models\Till\PaymentItem;
use App\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PaymentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'id' => 'nullable|string|exists:payments,id',
            'status' => ['required', Rule::in(['pending', 'completed'])],
            'payer' => 'required',
            'payer_type' => ['required', Rule::in(['user', 'branch'])],
            'payee' => 'required',
            'payee_type' => ['required', Rule::in(['user', 'branch'])],
            'paymentItem' => 'required|exists:payment_items,id',
            'billAmount' => 'required|numeric|min:1',
            'billCurrency' => 'required|exists:currencies,id',
            'paidAmount' => 'required|numeric|min:1',
            'paidCurrency' => 'required|exists:currencies,id',
            'exchangeRate' => 'nullable|string',
            'comment' => 'nullable|string',
        ];
    }

    private function getClient($id)
    {
        return User::find($id);
    }

    private function getBranch($id)
    {
        return Branch::find($id);
    }

    private $payer;

    private $payee;

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            //Check payer and payee
            $payerId = $this->request->get('payer');
            $this->payer = $this->request->get('payer_type') === 'branch' ? $this->getBranch($payerId) : $this->getClient($payerId);
            if (!$this->payer)
                return $validator->errors()->add('payer', 'Указанный плательщик не найден. ');

            $payeeId = $this->request->get('payee');
            $this->payee = $this->request->get('payee_type') === 'branch' ? $this->getBranch($payeeId) : $this->getClient($payeeId);
            if (!$this->payee)
                return $validator->errors()->add('payee', 'Указанный получатель не найден. ');

            if ($this->payer instanceof User && $this->payee instanceof User)
                return $validator->errors()->add('payer', 'Перевод денег между клиентами запрещен. ');

            $paymentItem = PaymentItem::find($this->request->get('paymentItem'));

            if ($this->payer instanceof Branch && $this->payee instanceof Branch
                && $paymentItem->title !== 'Перевод между счетами филиала' && $paymentItem->title !== 'Перевод между филиалами')
                return $validator->errors()->add('payer', 'Перевод денег между филиалами возможен для статей "Перевод между филиалами" или "Перевод между счетами филиала". ');

            $this->validatePaymentItems($paymentItem, $validator);

            $exchangeRate = null;

            //Check exchange rate
            if ($this->request->get('exchangeRate')) {

                $exchangeRate = ExchangeRate::where('id', $this->request->get('exchangeRate'))
                    ->where('id', $this->request->get('exchangeRate'))
                    ->where('from', $this->request->get('billCurrency'))
                    ->where('to', $this->request->get('paidCurrency'))
                    ->first();

                if (!$exchangeRate)
                    return $validator->errors()->add('exchangeRate', 'Указанный курс валют не найден или не соответствует выбранным валютам. ');

            }

            //Check payment amount
            if ($this->request->get('billCurrency') !== $this->request->get('paidCurrency')) {
                if (!$exchangeRate)
                    return $validator->errors()->add('exchangeRate', 'Валюта оплаты не соотсветсвует валюте платежа. Необходима конвертация. ');

                $amount = round($this->request->get('billAmount') * $exchangeRate->coefficient, 2);

                if ($amount - $this->request->get('paidAmount') != 0)
                    return $validator->errors()->add('paidAmount', 'Сумма оплаты не соотвествует требуемой с учетом конвертации. ');

            } else {
                if ($this->request->get('billAmount') !== $this->request->get('paidAmount'))
                    return $validator->errors()->add('paidAmount', 'Сумма к оплате не равняется требуемой сумме. ');
            }

            //Check payer account balance
            if ($this->request->get('payer_type') === 'branch') {
                $payerAccount = $this->payer->accounts()->where('currency_id', $this->request->get('paidCurrency'))->first();
                if (!$payerAccount)
                    return $validator->errors()->add('paidCurrency', 'Не найден счет плательщика в оплачиваемой валюте. ');

                $diff = $payerAccount->balance - $this->request->get('paidAmount');

                if ($diff < 0)
                    return $validator->errors()->add('payer', 'Недостаточно средств на счету плательщика - ' . $payerAccount->balance . ' ' . $payerAccount->currency->isoName) . '.';
            }
        });
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

        if ($this->request->get('billCurrency') === $this->request->get('paidCurrency'))
            return $validator->errors()->add('billCurrency', 'При переводе денег между счетами филиала валюта оплаты и валюта зачисления не должны совпадать. ');
    }

    private function validateMoneyExchange($validator)
    {
        if (!($this->payee instanceof Branch))
            return $validator->errors()->add('payee', 'При обмене валют получателем должен быть филиал, а плательщиком клиент. ');

        if (!($this->payer instanceof User))
            return $validator->errors()->add('payer', 'При обмене валют плательщиком должен быть клиент, а получателем филиал. ');

        if ($this->request->get('billCurrency') === $this->request->get('paidCurrency'))
            return $validator->errors()->add('billCurrency', 'При обмене валют требуемая валюта должна отличаться от валюты оплаты.');

        if (!$this->request->get('exchangeRate'))
            return $validator->errors()->add('exchangeRate', 'Курс обмена не указан. ');

        $payeeAccount = $this->payee->accounts()->where('currency_id', $this->request->get('billCurrency'))->firstOrFail();

        $diff = $payeeAccount->balance - $this->request->get('billAmount');

        if ($diff < 0)
            return $validator->errors()->add('payee', 'Недостаточно средств на счету филиала для выдачи валюты - ' . $payeeAccount->balance . ' ' . $payeeAccount->currency->isoName) . '.';

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
}
