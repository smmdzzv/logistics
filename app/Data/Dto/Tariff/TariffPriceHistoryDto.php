<?php
namespace App\Data\Dto\Tariff;

use Spatie\DataTransferObject\FlexibleDataTransferObject;

/**
 *
 * @author Sultonazar Mamadazizov <sultonazar.mamadazizov@mail.ru> on 01.07.2020
 */

class TariffPriceHistoryDto extends FlexibleDataTransferObject
{
    public ?string $id;

    public float $lowerLimit;

    public float $mediumLimit;

    public float $upperLimit;

    public float $pricePerCube;

    public float $discountForLowerLimit;

    public float $discountForMediumLimit;

    public float $pricePerExtraKg;

    public float $agreedPricePerKg;

    public float $maxWeightPerCube;

    public float $maxCubage;

    public string $tariff_id;

    public ?string $created_at;
}
