<?php

namespace App\Data\Dto\Item;

use Spatie\DataTransferObject\FlexibleDataTransferObject;

/**
 *
 * @author Sultonazar Mamadazizov <sultonazar.mamadazizov@mail.ru> on 01.07.2020
 */
class ItemDto extends FlexibleDataTransferObject
{
    public ?string $id;

    public string $name;

    public ?string $unit;

    public bool $onlyCustomPrice;

    public bool $onlyAgreedPrice;

    public bool $applyDiscount;

    public bool $calculateByNormAndWeight;

    public array $customsCodes;
}
