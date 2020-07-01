<?php
/**
 *
 * @author Sultonazar Mamadazizov <sultonazar.mamadazizov@mail.ru> on 01.07.2020
 */

namespace App\Data\Dto\Customs;


use Spatie\DataTransferObject\FlexibleDataTransferObject;

class CustomsCodeTaxDto extends FlexibleDataTransferObject
{
    public float $price;

    public float $interestRate;

    public float $vat;

    public float $totalRate;

    public bool $isCalculatedByPiece;
}
