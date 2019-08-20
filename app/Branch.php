<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property string name
 */
class Branch extends Model
{
    protected $guarded = [];

    public function users(){
        return $this->hasMany(User::class );
    }

    public function storedItems(){
        return $this->hasMany(StoredItem::class);
    }
}
