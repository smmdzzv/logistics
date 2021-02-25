<?php
/**
 * @author Sultonazar Mamadazizov <sultonazar.mamadazizov@mail.ru>
 */

namespace App\Http\Requests\Till;


use App\Http\Requests\BaseValidationPolicyDecorator;
use App\Models\Till\Payment;

class CompletedPaymentValidationPolicy extends PaymentValidationPolicy
{
    public function apply()
    {
        $id = $this->request->get('id');
        if($id && !Payment::find($id)->approved){
            return $this->validator->errors()->add('status', 'Завяка не была одобрена');
        }

        if(!$this->request->user()->hasAnyRole(['cashier', 'admin']))
            return $this->validator->errors()->add('status', 'Вы можете создавать только заявки');

        $this->validationPolicy->apply();
    }
}
