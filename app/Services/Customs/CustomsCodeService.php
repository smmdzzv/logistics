<?php

namespace App\Services\Customs;

use App\Data\Dto\Customs\CustomsCodeDto;
use App\Models\Customs\CustomsCode;

/**
 *
 * @author Sultonazar Mamadazizov <sultonazar.mamadazizov@mail.ru> on 30.06.2020
 */
class CustomsCodeService
{
    public function store(CustomsCodeDto $data): CustomsCode
    {
        return CustomsCode::create($data->toArray());
    }

    public function update(CustomsCode $code, CustomsCodeDto $data): CustomsCode
    {
        $code->update($data->toArray());
        return $code;
    }
}
