<?php
/**
 * @author Sultonazar Mamadazizov <sultonazar.mamadazizov@mail.ru>
 */

namespace App\Http\Requests;


use Illuminate\Http\Request;
use Illuminate\Validation\Validator;

class BaseValidationPolicyDecorator implements RequestValidationPolicy
{

    protected RequestValidationPolicy $validationPolicy;

    protected Validator $validator;

    protected Request $request;

    public function __construct(RequestValidationPolicy $policy, Validator $validator, Request $request)
    {
        $this->validationPolicy = $policy;

        $this->validator = $validator;

        $this->request = $request;
    }

    public function apply()
    {
        $this->validationPolicy->apply();
    }
}
