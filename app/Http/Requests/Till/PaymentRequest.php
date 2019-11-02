<?php

namespace App\Http\Requests\Till;

use App\Models\Currency;
use App\Models\Order;
use App\Models\Till\Account;
use App\Models\Till\MoneyExchange;
use App\Models\Users\Driver;
use App\Models\Users\Client;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

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
            'payerId' => 'required|exists:users,id',
            'paymentItemId' => 'required|exists:payment_items,id',
            'amount' => 'required|numeric|min:1',
            'comment' => 'nullable|string',
            'status' => 'required|string'
        ];
    }

    /**
     * Configure the validator instance.
     *
     * @param Validator $validator
     * @return void
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $amount = $this->request->get('amount');
//            $convertedPrice = null;
            $currency = Currency::find($this->request->get('currencyId'));

//            $client = Client::find($this->request->get('payerId'));

            if (!$currency)
                return $validator->errors()->add('currencyId', 'Указанная валюта не используется системой');

            $accountTo = Account::with('currency')->find($this->request->get('accountTo'));
            if (!$accountTo)
                return $validator->errors()->add('accountTo', 'Указанный счет не найден');

            if ($accountTo->currency->id !== $currency->id) {
                $exchange = MoneyExchange::with(['fromCurrency', 'toCurrency'])->find($this->request->get('exchangeId'));
                if (!$exchange)
                    return $validator->errors()->add('exchangeId', 'Указанный курс валют не найден');
                if ($exchange->fromCurrency->id !== $currency->id || $exchange->toCurrency->id !== $accountTo->currency->id)
                    return $validator->errors()->add('exchangeId', 'Указанный курс не соотвествует конвертируемым валютам');

//                $convertedPrice = $amount * $exchange->coefficient;
            }

//            $orderId = $this->request->get('orderId');
//            if ($orderId) {
//                $order = Order::find($orderId);
//                if (!$order)
//                    return $validator->errors()->add('orderId', 'Указанный заказ не найден');
//
//                if($order->payment)
//                    return $validator->errors()->add('orderId', 'Заказ от '.$order->created_at.' оплачен');
//
//                $paid = $convertedPrice ?? $amount;
//
//                if ($order->totalPrice !== (double)$paid)
//                    return $validator->errors()->add('orderId', 'Внесенная сумма не равна сумме заказа');
//            }
        });
    }
}
