<?php

namespace App\Models\StoredItems;


use App\Models\BaseModel;
use App\Models\Branches\Storage;
use App\Models\Order\OrderPaymentItem;
use App\Models\Trip;
use App\StoredItems\StorageHistory;
use App\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;

class StoredItem extends BaseModel
{
    use SoftDeletes;

    protected $guarded = [];

    /**
     * @param $query
     * @return mixed
     * TODO explanation
     */
    public function scopeAvailable($query){
        return $query->whereDoesntHave('tripHistory');
    }

    public function scopeStorage($query, $id){
        return $query->whereHas('storageHistory', function (Builder $query) use ($id) {
            $query->where('storage_id', $id);
        });
    }

    public function scopeUnpaid($query){
        return $query->whereDoesntHave('orderPaymentItems', function (Builder $query) {
            $query->whereHas('orderPayment', function (Builder $query){
                $query->whereHas('payment', function (Builder $query){
                    $query->where('status', 'completed')->whereHas('paymentItem', function (Builder $query){
                        $query->where('title', '=','Списание с баланса');
                    });
                });
            });
        });
    }

    public function orderPaymentItems(){
        return $this->hasMany(OrderPaymentItem::class);
    }

    public function info(){
        return $this->belongsTo(StoredItemInfo::class, 'stored_item_info_id');
    }

    public function storageHistories(){
        return $this->hasMany(StorageHistory::class);
    }

    public function storageHistory(){
        return $this->hasOne(StorageHistory::class)->latest();
    }

    public function storage(){
        return $this->stores()->latest()->limit(1);
    }

    public function stores(){
        return $this->belongsToMany(
            Storage::class,
            'storage_histories',
            'stored_item_id',
            'storage_id')
            ->using('App\Models\Pivots\BasePivot')
            ->withTimestamps();
    }

    public function trips()
    {
        return $this->belongsToMany(
            Trip::class,
            'stored_item_trip_histories',
            'stored_item_id',
            'trip_id')
            ->using('App\Models\Pivots\BasePivot')
            ->wherePivot('deleted_at', null)
            ->withTimestamps();
    }

    public function tripHistories(){
        return $this->hasMany(StoredItemTripHistory::class)->withTrashed();
    }

    public function tripHistory(){
        return $this->hasOne(StoredItemTripHistory::class)->latest();
    }

    public function deletedBy(){
        return $this->belongsTo(User::class);
    }
}
