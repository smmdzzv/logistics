<?php

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
