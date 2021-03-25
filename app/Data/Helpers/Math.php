<?php
/**
 * @author Sultonazar Mamadazizov <sultonazar.mamadazizov@mail.ru>
 */

namespace App\Data\Helpers;


class Math
{
    public static function roundUp(float $value, int $precision): float
    {
        $factor = pow(10, $precision);
        return ceil($value * $factor) / $factor;
    }
}
