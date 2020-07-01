<?php

namespace App\Models\Customs;

use App\Models\BaseModel;

class CustomsCode extends BaseModel
{
    protected $guarded = [];

    protected $casts = ['isCalculatedByPiece' => 'boolean'];

    public function taxes()
    {
        return $this->hasMany(CustomsCodeTax::class, 'customs_code_id');
    }
}
