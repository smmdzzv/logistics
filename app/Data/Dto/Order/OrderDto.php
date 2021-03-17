<?php

namespace App\Data\Dto\Order;

use App\Data\Dto\StoredItem\StoredItemInfoDto;
use Spatie\DataTransferObject\FlexibleDataTransferObject;

/**
 *
 * @author Sultonazar Mamadazizov <sultonazar.mamadazizov@mail.ru> on 03.07.2020
 */
class OrderDto extends FlexibleDataTransferObject
{
    public ?string $id;

    public string $owner_id;

    public string $branch_id;

    public string $status = 'active';

    public float $totalCount = 0;

    public float $totalPlaceCount = 0;

    public float $totalWeight = 0;

    public float $totalCubage = 0;

    public float $totalPrice = 0;

    public float $totalDiscount = 0;

    public array $storedItemInfos;

    public array $customPrices = [];

    public function __construct(array $parameters = [])
    {
        parent::__construct($parameters);

        $this->storedItemInfos = collect($parameters['storedItemInfos'])
            ->map(function ($info) {
                $info['weight'] = (float)$info['weight'];
                $info['height'] = (float)$info['height'];
                $info['width'] = (float)$info['width'];
                $info['length'] = (float)$info['length'];
                $info['count'] = (int)$info['count'];
//                $info['customPrice'] = $info['customPrice'] ? (float)$info['customPrice'] : null;
                return new StoredItemInfoDto($info);
            })->all();
    }
}
