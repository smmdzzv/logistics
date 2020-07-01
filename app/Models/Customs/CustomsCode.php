<?php
/**
 *
 * @author Sultonazar Mamadazizov <sultonazar.mamadazizov@mail.ru>
 */

namespace App\Models\Customs;

use App\Models\BaseModel;

class CustomsCode extends BaseModel
{
    protected $guarded = [];

    public function taxes()
    {
        return $this->hasMany(CustomsCodeTax::class, 'customs_code_id');
    }

    public function tax()
    {
        return $this->hasOne(CustomsCodeTax::class, 'customs_code_id')->latest();
    }
}
