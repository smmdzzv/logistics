<?php

namespace App\Models;


class Country extends BaseModel
{
    public function branches()
    {
        return $this->hasMany(Branch::class, 'country');
    }

    public function currencies()
    {
        return $this->hasMany(Currency::class);
    }
}
