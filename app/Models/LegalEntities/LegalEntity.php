<?php

namespace App\Models\LegalEntities;

use App\Models\BaseModel;
use App\Models\Till\Account;
use Illuminate\Database\Eloquent\Model;

class LegalEntity extends BaseModel
{
    public function accounts(){
        return $this->morphMany(Account::class, 'owner');
    }
}
