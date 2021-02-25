<?php

namespace App\Http\Requests\Till;

use App\Http\Requests\BaseValidationPolicy;
use App\Http\Requests\BaseValidationPolicyDecorator;
use App\Http\Requests\RequestValidationPolicy;
use App\Models\Branch;
use App\Models\Currency;
use App\Models\Till\ExchangeRate;
use App\Models\Till\PaymentItem;
use App\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PaymentRequest extends FormRequest
{
    private $payer;

    private $payee;

    private RequestValidationPolicy $validationRules;

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
            'payee' => 'nullable|string',
            'payee_type' => ['required', Rule::in(['user', 'branch'])],
            'paymentItem' => 'required|exists:payment_items,id',
            'billAmount' => 'required|numeric|min:1',
            'billCurrency' => 'required|exists:currencies,id',
            'paidAmountInBillCurrency' => 'required|numeric|min:0',
            'paidAmountInSecondCurrency' => 'nullable|numeric|min:0',
            'secondPaidCurrency' => 'nullable|exists:currencies,id',
            'exchangeRate' => 'nullable|string',
            'customExchangeRate' => 'nullable|string',
            'comment' => 'nullable|string',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $payer_id = $this->request->get('payer');
            $payer = $this->request->get('payer_type') === 'branch' ? Branch::find($payer_id) : User::find($payer_id);

            $payee_id = $this->request->get('payee');
            $payee = $this->request->get('payee_type') === 'branch' ? Branch::find($payee_id) : User::find($payee_id);

            $this->validationRules = new BaseValidationPolicyDecorator(new BaseValidationPolicy(), $validator, request());

            $this->validationRules =
                new PaymentSubjectsValidationPolicy($this->validationRules, $validator, request(), $payer, $payee);

            if ($this->request->get('status') == 'completed')
                $this->validationRules =
                    new CompletedPaymentValidationPolicy($this->validationRules, $validator, request(), $payer, $payee);

            $this->validationRules = new PaymentItemValidationPolicy($this->validationRules, $validator, request(), $payer, $payee);

            if ($this->request->get('exchangeRate'))
                $this->validationRules =
                    new ExchangeValidationPolicy($this->validationRules, $validator, request(), $payer, $payee);

            $this->validationRules =
                new PaymentAmountValidationPolicy($this->validationRules, $validator, request(), $payer, $payee);

            $this->validationRules =
                new PaymentPayerAccountBalanceValidationPolicy($this->validationRules, $validator, request(), $payer, $payee);

            $this->validationRules->apply();

            //Check payer and payee
//            $payerId = $this->request->get('payer');
//            $this->payer = $this->request->get('payer_type') === 'branch' ? $this->getBranch($payerId) : $this->getClient($payerId);
//            if (!$this->payer)
//                return $validator->errors()->add('payer', 'Указанный плательщик не найден. ');

//            $payeeId = $this->request->get('payee');
//            if ($payeeId) {
//                $this->payee = $this->request->get('payee_type') === 'branch' ? $this->getBranch($payeeId) : $this->getClient($payeeId);
//                if (!$this->payee)
//                    return $validator->errors()->add('payee', 'Указанный получатель не найден. ');
//            }

//            if ($this->payer instanceof User && $this->payee instanceof User)
//                return $validator->errors()->add('payer', 'Перевод денег между клиентами запрещен. ');

//            $paymentItem = PaymentItem::find($this->request->get('paymentItem'));
//
//            if ($this->payer instanceof Branch && $this->payee instanceof Branch
//                && $paymentItem->title !== 'Перевод между счетами филиала' && $paymentItem->title !== 'Перевод между филиалами')
//                return $validator->errors()->add('payer', 'Перевод денег между филиалами возможен для статей "Перевод между филиалами" или "Перевод между счетами филиала". ');
//
//            $this->validatePaymentItems($paymentItem, $validator);

//            $exchangeRate = null;
//
//            //Check exchange rate
//            if ($this->request->get('exchangeRate')) {
//
//                $exchangeRate = ExchangeRate::where('id', $this->request->get('exchangeRate'))
//                    ->where('id', $this->request->get('exchangeRate'))
//                    ->where('from_currency_id', $this->request->get('secondPaidCurrency'))
//                    ->where('to_currency_id', $this->request->get('billCurrency'))
//                    ->first();
//
//                if (!$exchangeRate)
//                    return $validator->errors()->add('exchangeRate', 'Указанный курс валют не найден или не соответствует выбранным валютам. ');
//
//            }

            //Check payment amount
//            if ($this->request->get('secondPaidCurrency') && $this->request->get('paidAmountInSecondCurrency') > 0) {
//                if (!$exchangeRate && !$this->request->get('customExchangeRate'))
//                    return $validator->errors()->add('secondPaidCurrency', 'Для указанной валюты Необходима конвертация. ');
//
////                $amount = round($this->request->get('billAmount') * $exchangeRate->coefficient, 2);
//                $rate = $exchangeRate->coefficient ?? $this->request->get('customExchangeRate');
//                $amount = round($this->request->get('paidAmountInBillCurrency') + $this->request->get('paidAmountInSecondCurrency') * $rate, 2);
//
//                if ($this->request->get('billAmount') - $amount != 0)
//                    return $validator->errors()->add('paidAmountInBillCurrency',
//                        'Сумма оплаты не соотвествует требуемой с учетом конвертации. Требуемая ' . $this->request->get('billAmount') . '. Оплаченная ' . $amount);
//
//            } else {
//                if ($this->request->get('billAmount') !== $this->request->get('paidAmountInBillCurrency'))
//                    return $validator->errors()->add('paidAmountInBillCurrency', 'Сумма к оплате не равняется требуемой сумме. ');
//            }

            //Check payer account balance
//            if ($this->request->get('payer_type') === 'branch') {
//                $payerAccountBillCurrency = $this->payer->accounts()->where('currency_id', $this->request->get('billCurrency'))->first();
//                $payerAccountSecondCurrency = $this->payer->accounts()->where('currency_id', $this->request->get('secondPaidCurrency'))->first();
//
//                if (!$payerAccountBillCurrency)
//                    return $validator->errors()->add('billCurrency', 'Не найден счет плательщика в оплачиваемой валюте. ');
//
//                if ($payerAccountBillCurrency->balance - $this->request->get('paidAmountInBillCurrency') < 0)
//                    return $validator->errors()->add('payer',
//                            'Недостаточно средств на счету плательщика - ' . $payerAccountBillCurrency->balance . ' ' . $payerAccountBillCurrency->currency->isoName) . '.';
//
//                if ($this->request->get('paidAmountInSecondCurrency') > 0) {
//                    if (!$payerAccountSecondCurrency)
//                        return $validator->errors()->add('secondPaidCurrency', 'Не найден счет плательщика в оплачиваемой валюте. ');
//
//                    if ($payerAccountSecondCurrency->balance - $this->request->get('paidAmountInSecondCurrency') < 0)
//                        return $validator->errors()->add('payer',
//                                'Недостаточно средств на счету плательщика - ' . $payerAccountSecondCurrency->balance . ' ' . $payerAccountSecondCurrency->currency->isoName) . '.';
//
//                }
//            }
        });
    }

//    private function getBranch($id)
//    {
//        return Branch::find($id);
//    }
//
//    private function getClient($id)
//    {
//        return User::find($id);
//    }

//    private function validatePaymentItems(PaymentItem $paymentItem, $validator)
//    {
//        switch ($paymentItem->title) {
//            case 'Пополнение баланса':
//                $this->validateBalanceReplenishment($validator);
//                break;
//            case 'Перевод между филиалами':
//                $this->validateTransferBetweenBranches($validator);
//                break;
//            case 'Перевод между счетами филиала':
//                $this->validateTransferBetweenBranchAccounts($validator);
//                break;
//            case 'Прием наличных средств':
//                $this->validateCashAccepting($validator);
//                break;
//            case 'Аванс':
//            case 'Зарплата':
//            case 'Выдача наличных средств':
//                $this->validateCashWithdrawal($validator);
//                break;
//            case 'Обмен валют':
//                $this->validateMoneyExchange($validator);
//                break;
//        }
//    }
//
//    private function validateBalanceReplenishment($validator)
//    {
//        if (!($this->payer instanceof User))
//            return $validator->errors()->add('payer', 'При пополнении баланса плательщиком должен являться клиент. ');
//
//        if (Currency::find($this->request->get('billCurrency'))->isoName !== 'USD')
//            return $validator->errors()->add('billCurrency', 'При пополнении баланса счет должен выставляться в долларах. ');
//    }
//
//    private function validateTransferBetweenBranches($validator)
//    {
//        if (!($this->payer instanceof Branch))
//            return $validator->errors()->add('payer', 'При переводе денег между филиалами плательщиком должен быть филиал. ');
//
//        if (!($this->payee instanceof Branch))
//            return $validator->errors()->add('payee', 'При переводе денег между филиалами получателем должен быть филиал. ');
//
//        if ($this->payer->id === $this->payee->id)
//            return $validator->errors()->add('payee', 'При переводе денег между филиалами получатель и плательщик должны отличаться. ');
//    }
//
//    private function validateTransferBetweenBranchAccounts($validator)
//    {
//        if ($this->payer->id !== $this->payee->id)
//            return $validator->errors()->add('payer', 'При переводе денег между счетами филиала плательщиком и получателем должен быть один филиал. ');
//
//        if ($this->request->get('billCurrency') === $this->request->get('secondPaidCurrency'))
//            return $validator->errors()->add('billCurrency', 'При переводе денег между счетами филиала валюта оплаты и валюта зачисления не должны совпадать. ');
//
//        if ($this->request->get('paidAmountInBillCurrency') > 0)
//            return $validator->errors()->add('paidAmountInBillCurrency', 'При переводе денег между счетами филиала сумма в валюте оплаты должны быть равна 0. ');
//    }
//
//    private function validateCashAccepting($validator)
//    {
//        if (!($this->payer instanceof User))
//            return $validator->errors()->add('payer', 'При приеме наличных средств плательщиком должен быть указан пользователь. ');
//
//        if (!($this->payee instanceof Branch))
//            return $validator->errors()->add('payee', 'При приеме наличных средств получателем должен быть указана филиал. ');
//    }
//
//    private function validateCashWithdrawal($validator)
//    {
//        if (!($this->payer instanceof Branch))
//            return $validator->errors()->add('payer', 'При выдаче наличных средств плательщиком должен быть указан филиал. ');
//
//        if (!($this->payee instanceof User))
//            return $validator->errors()->add('payee', 'При выдаче наличных средств получателем должен быть указана пользователь. ');
//    }
//
//    private function validateMoneyExchange($validator)
//    {
//        if (!($this->payee instanceof Branch))
//            return $validator->errors()->add('payee', 'При обмене валют получателем должен быть филиал, а плательщиком клиент. ');
//
//        if (!($this->payer instanceof User))
//            return $validator->errors()->add('payer', 'При обмене валют плательщиком должен быть клиент, а получателем филиал. ');
//
//        if ($this->request->get('billCurrency') === $this->request->get('secondPaidCurrency'))
//            return $validator->errors()->add('secondPaidCurrency', 'При обмене валют требуемая валюта должна отличаться от валюты оплаты.');
//
//        if (!$this->request->get('exchangeRate'))
//            return $validator->errors()->add('exchangeRate', 'Курс обмена не указан. ');
//
//        if ($this->request->get('paidAmountInBillCurrency') > 0)
//            return $validator->errors()->add('paidAmountInBillCurrency', 'Сумма в целевой валюте должна равняться нулю. ');
//
//        //check branch payment account
//        $payeeAccount = $this->payee->accounts()->where('currency_id', $this->request->get('billCurrency'))->firstOrFail();
//
//        if ($payeeAccount->balance - $this->request->get('billAmount') < 0)
//            return $validator->errors()->add('payee',
//                    'Недостаточно средств на счету филиала для выдачи валюты - '
//                    . $payeeAccount->balance
//                    . ' ' . $payeeAccount->currency->isoName) . '. ';
//
//    }
}
