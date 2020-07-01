<?php

/**
 *
 * @author Sultonazar Mamadazizov <sultonazar.mamadazizov@mail.ru>
 */

namespace App\Models;


use App\Data\Userstamps\Userstamps;
use Illuminate\Database\Eloquent\Model;
use Rorecek\Ulid\HasUlid;

class BaseModel extends Model
{
    use HasUlid, Userstamps;

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    public static $snakeAttributes = false;

    protected $casts = ['id' => 'string'];
}
