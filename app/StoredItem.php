<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property double weight
 * @property double height
 * @property double width
 * @property double length
 * @property int count
 * @property int item_id
 * @property int owner_id
 * @property  int branch_id
 */
class StoredItem extends Model
{
    public function item(){
        return $this->belongsTo(Item::class);
    }

    public function owner(){
        return $this->belongsTo(User::class);
    }

    public function branch(){
        return$this->belongsTo(Branch::class);
    }
}
