<?php

namespace App\Data\Dto\StoredItem;

use App\Common\NumericHelper;
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

    public int $placeCount;

//    public ?float $customPrice;

    public ?string $shop;

    public string $item_id;

    public string $owner_id;

    public ?string $order_id;

    public string $branch_id;

    public string $tariff_id;

//    public string $tariff_price_history_id;

    public string $customs_code_id;

    public string $customs_code_tax_id;

    public function __construct(array $parameters = [])
    {
        $parameters['width'] = NumericHelper::roundFloatVal($parameters['width'], 3);
        $parameters['height'] = NumericHelper::roundFloatVal($parameters['height'], 3);
        $parameters['length'] = NumericHelper::roundFloatVal($parameters['length'], 3);
        $parameters['weight'] = NumericHelper::roundFloatVal($parameters['weight'], 3);
        $parameters['count'] = (int)$parameters['count'];
        $parameters['placeCount'] = (int)$parameters['placeCount'];

        parent::__construct($parameters);
    }
}
