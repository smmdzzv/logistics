<?php
/**
 *
 * @author Sultonazar Mamadazizov <sultonazar.mamadazizov@mail.ru> on 30.06.2020
 */

namespace App\Data\Dto\Customs;

use Spatie\DataTransferObject\FlexibleDataTransferObject;


class CustomsCodeDto extends FlexibleDataTransferObject
{
    public ?string $id;

    public string $name;

    public string $code;
}
