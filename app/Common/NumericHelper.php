<?php
/**
 *
 * @author Sultonazar Mamadazizov <sultonazar.mamadazizov@mail.ru> on 07.07.2020
 */

namespace App\Common;


class NumericHelper
{
    public static function roundFloatVal($value, $precision = 2): float
    {
        return round((float)$value, $precision);
    }

}
