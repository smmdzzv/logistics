<?php
/**
 *
 * @author Sultonazar Mamadazizov <sultonazar.mamadazizov@mail.ru> on 01.07.2020
 */

namespace App\Services\Customs;


use App\Data\Dto\Customs\CustomsCodeDto;
use App\Data\Dto\Customs\CustomsCodeTaxDto;
use App\Models\Customs\CustomsCode;

class CustomsCodeAndTaxService
{
    private CustomsCodeService $codeService;

    private CustomsCodeTaxService $taxService;

    public function __construct(CustomsCodeService $codeService, CustomsCodeTaxService $taxService)
    {
        $this->codeService = $codeService;
        $this->taxService = $taxService;
    }

    public function store(CustomsCodeDto $codeDto, CustomsCodeTaxDto $taxDto): CustomsCode
    {
        $code = $this->codeService->store($codeDto);
        $code->taxes()->create($taxDto->toArray());
        return $code;
    }

    public function update(CustomsCode $code, CustomsCodeDto $codeDto, CustomsCodeTaxDto $taxDto): CustomsCode
    {
        $code = $this->codeService->update($code, $codeDto);
        $code->taxes()->create($taxDto->toArray());
        return $code;
    }
}
