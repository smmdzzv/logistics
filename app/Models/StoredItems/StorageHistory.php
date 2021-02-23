<?php
/**
 *
 * @author Sultonazar Mamadazizov <sultonazar.mamadazizov@mail.ru>
 */

namespace App\Models\StoredItems;

use App\Models\BaseModel;
use App\Models\Branches\Storage;
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
