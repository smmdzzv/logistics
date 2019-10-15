<?php

namespace App\Http\Requests;

use App\Models\Currency;
use App\Models\Order;
use App\Models\Till\Account;
use App\Models\Till\MoneyExchange;
use App\Models\Users\Client;
use Illuminate\Foundation\Http\FormRequest;

class OutgoingPaymentRequest extends FormRequest
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
            'paymentItemId' => 'required|exists:payment_items,id',
            'amount' => 'required|numeric|min:1',
            'exchangeId' => 'nullable|exists:money_exchanges,id'
        ];
    }
}


