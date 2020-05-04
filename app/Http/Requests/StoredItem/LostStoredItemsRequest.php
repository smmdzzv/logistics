<?php


namespace App\Http\Requests\StoredItem;


use Illuminate\Foundation\Http\FormRequest;

class LostStoredItemsRequest extends FormRequest
{
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
            'storedItems' => 'required|array',
            'storedItems*' => 'exists:stored_items,id',
            'compensation' => 'required|numeric'
        ];
    }
}
