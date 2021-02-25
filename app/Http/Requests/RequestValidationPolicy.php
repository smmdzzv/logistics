<?php
/**
 * @author Sultonazar Mamadazizov <sultonazar.mamadazizov@mail.ru>
 */

namespace App\Http\Requests;


interface RequestValidationPolicy
{
    public function apply();
}
