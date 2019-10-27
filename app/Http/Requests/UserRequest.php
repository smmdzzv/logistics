<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserRequest extends FormRequest
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
            'name' => 'nullable|string|max:255',
            'roles' => 'required|array',
            'branch' => 'required|exists:branches,id',
            'position' => 'nullable|string',
            'phone' => 'nullable|string',
            'password' => [
                Rule::requiredIf(function (){
                    return !isset(request()->user);
                })
            ],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore(
                    request()->user ? request()->user->id : '-1'
                )
            ],
            'code' => [
                'nullable',
                'string',
                Rule::unique('users')->ignore(
                    request()->user ? request()->user->id : '-1'
                )
            ],
        ];
    }
}
