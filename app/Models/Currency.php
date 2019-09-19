<?php

namespace App\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string shortName
 * @property string name
 */
class Currency extends BaseModel
{
    public function accounts(){
        return $this->hasMany(Account::class);
    }
}
