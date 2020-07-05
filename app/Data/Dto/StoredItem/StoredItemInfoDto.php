<?php

namespace App\Data\Dto\StoredItem;

use Spatie\DataTransferObject\FlexibleDataTransferObject;

/**
 *
 * @author Sultonazar Mamadazizov <sultonazar.mamadazizov@mail.ru> on 03.07.2020
 */
class StoredItemInfoDto extends FlexibleDataTransferObject
{
    public ?string $id;

    public float $width;

    public float $height;

    public float $length;

    public float $weight;

    public int $count;

//    public ?float $customPrice;

    public ?string $shop;

    public string $item_id;

    public string $owner_id;

    public ?string $order_id;

    public string $branch_id;

    public string $tariff_id;

    public string $tariff_price_history_id;

    public string $customs_code_tax_id;
}
