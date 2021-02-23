<?php

/**
 *
 * @author Sultonazar Mamadazizov <sultonazar.mamadazizov@mail.ru>
 */

namespace App\Models;


use App\Data\Userstamps\Userstamps;
use App\Traits\UsesUUID;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model
{
    use UsesUUID;

    use Userstamps;

    public $incrementing = false;

    protected $primaryKey = 'id';

    protected $keyType = 'uuid';

    public static $snakeAttributes = false;

    protected $guarded = [];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
