<?php
/**
 *
 * @author Sultonazar Mamadazizov <sultonazar.mamadazizov@mail.ru> on 01.07.2020
 */

namespace App\Models\Customs;

use App\Models\BaseModel;

class CustomsCodeTax extends BaseModel
{
    protected $guarded = [];

    protected $casts = ['isCalculatedByPiece' => 'boolean'];

    public function code()
    {
        return $this->belongsTo(CustomsCode::class, 'customs_code_id');
    }
}
