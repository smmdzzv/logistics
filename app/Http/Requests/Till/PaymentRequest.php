<?php

namespace App\Http\Requests\Till;

use App\Http\Requests\BaseValidationPolicy;
use App\Http\Requests\BaseValidationPolicyDecorator;
use App\Http\Requests\RequestValidationPolicy;
use App\Models\Branch;
use App\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PaymentRequest extends FormRequest
{
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
        });
    }
}
