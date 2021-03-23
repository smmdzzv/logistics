<?php

namespace App\Models\StoredItems;


use App\Models\BaseModel;
use App\Models\Branches\Storage;
use App\Models\Trip;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property string status
 * @property string id
 * @property StorageHistory storageHistory
 * @property StoredItemTripHistory tripHistory
 */
class StoredItem extends BaseModel
{
    use SoftDeletes;

    protected $guarded = [];

    public const STATUS_STORED = 'stored';

    public const STATUS_DELIVERED = 'delivered';

    public const STATUS_TRANSIT = 'transit';

    public const STATUS_LOST = 'lost';

    public const STATUS_DELETED = 'deleted';

    /**
     * @param $query
     * @return mixed
     * TODO explanation
     */
    public function scopeAvailable($query)
    {
        return $query->whereDoesntHave('tripHistory');
    }

    public function scopeNotDelivered($query)
    {
        return $query->where('status', '!=', self::STATUS_DELIVERED);
    }

    public function scopeStorage($query, $id)
    {
        return $query->whereHas('storageHistory', function (Builder $query) use ($id) {
            $query->where('storage_id', $id);
        });
    }

    public function scopeUnpaid($query)
    {
        return $query->whereDoesntHave('clientItemsSelections', function (Builder $query) {
            $query->whereHas('payments', function (Builder $query) {
                $query->where('status', 'completed')
                    ->whereHas('paymentItem', function (Builder $query) {
                        $query->where('title', '=', 'Списание с баланса');
                    });
            });
        });
    }

    public function clientItemsSelections()
    {
        return $this->belongsToMany(ItemsSelection::class)
            ->using('App\Models\Pivots\BasePivot');
    }

//    public function orderPaymentItems()
//    {
//        return $this->hasMany(OrderPaymentItem::class);
//    }

    public function info()
    {
        return $this->belongsTo(StoredItemInfo::class, 'stored_item_info_id');
    }

    public function storageHistories()
    {
        return $this->hasMany(StorageHistory::class);
    }

    public function storageHistory()
    {
        return $this->hasOne(StorageHistory::class)->latest();
    }

    public function storage()
    {
        return $this->stores()->latest()->limit(1);
    }

    public function stores()
    {
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

    public function tripHistories()
    {
        return $this->hasMany(StoredItemTripHistory::class);
    }

    public function tripHistory()
    {
        return $this->hasOne(StoredItemTripHistory::class)->latest();
    }

//    public function deletedBy(){
//        return $this->belongsTo(User::class);
//    }


    //Statuses
    public function setStoredStatus()
    {
        $this->status = self::STATUS_STORED;
    }

    public function setTransitStatus()
    {
        $this->status = self::STATUS_TRANSIT;
    }

    public function setDeliveredStatus()
    {
        $this->status = self::STATUS_DELIVERED;
    }

//    public function setDeletedStatus()
//    {
//        $this->status = 'deleted';
//    }

    public function setLostStatus()
    {
        $this->status = self::STATUS_LOST;
    }
}
