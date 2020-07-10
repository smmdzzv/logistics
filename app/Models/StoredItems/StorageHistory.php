<?php
/**
 *
 * @author Sultonazar Mamadazizov <sultonazar.mamadazizov@mail.ru>
 */

namespace App\StoredItems;

use App\Models\BaseModel;
use App\Models\Branch;
use App\Models\Branches\Storage;
use App\Models\StoredItems\StoredItem;
use App\User;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int deleted_at
 * @property string id
 * @property string deleted_by_id
 * @property string updated_at
 * @property string created_by_id
 * @property string created_at
 */
class StorageHistory extends BaseModel
{
    use SoftDeletes;

    public function storage()
    {
        return $this->belongsTo(Storage::class);
    }

    public function storedItem()
    {
        return $this->belongsTo(StoredItem::class);
    }
}
