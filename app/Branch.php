<?php

namespace App;

use App\Models\Country;
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

    public function director(){
        return $this->belongsTo(User::class, 'director');
    }

    public function country(){
        return $this->belongsTo(Country::class, 'country');
    }

    public function storedItems(){
        return $this->hasMany(StoredItem::class);
    }

    public function tariffPriceHistories(){
        return $this->hasMany(TariffPriceHistory::class);
    }

    public function orders(){
        return $this->hasMany(Order::class, 'branch');
    }
}
