<?php
/**
 * @author Sultonazar Mamadazizov <sultonazar.mamadazizov@mail.ru>
 */

namespace App\Http\Requests\Till;


use App\Http\Requests\BaseValidationPolicyDecorator;
use App\Http\Requests\RequestValidationPolicy;
use Illuminate\Http\Request;
use Illuminate\Validation\Validator;

class PaymentValidationPolicy extends BaseValidationPolicyDecorator
{
    protected $payer;

    protected $payee;

    public function __construct(RequestValidationPolicy $policy, Validator $validator, Request $request, $payer, $payee)
    {
        $this->payer = $payer;

        $this->payee = $payee;

        parent::__construct($policy, $validator, $request);
    }
}
