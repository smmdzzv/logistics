<?php
/**
 * @author Sultonazar Mamadazizov <sultonazar.mamadazizov@mail.ru>
 */

namespace App\Data\Dto\Trip;


use App\Models\Trip;
use Spatie\DataTransferObject\FlexibleDataTransferObject;

class CompletedTripDto extends FlexibleDataTransferObject
{
    public Trip $trip;

    public float $mileageAfter;

    public float $consumption;

    public string $comment;

    public float $driverPaymentAmount;


}
