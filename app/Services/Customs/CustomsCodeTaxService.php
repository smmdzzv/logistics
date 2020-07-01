<?php
/**
 *
 * @author Sultonazar Mamadazizov <sultonazar.mamadazizov@mail.ru> on 01.07.2020
 */

namespace App\Services\Customs;


use App\Data\Dto\Customs\CustomsCodeTaxDto;
use App\Models\Customs\CustomsCodeTax;

class CustomsCodeTaxService
{
    public function store(CustomsCodeTaxDto $dto): CustomsCodeTax
    {
        return CustomsCodeTax::create($dto->toArray());
    }

    public function update(CustomsCodeTax $tax, CustomsCodeTaxDto $dto): CustomsCodeTax
    {
        $tax->update($dto->toArray());
        return $tax;
    }
}
