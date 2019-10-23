<?php

namespace App;

use App\Models\Branch;
use App\Models\Order;
use App\Models\Position;
use App\Models\Role;
use App\Models\StoredItem;
use App\Models\Till\Account;
use App\Models\Till\Payment;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Rorecek\Ulid\HasUlid;

class User extends Authenticatable
{
    use HasUlid;

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'api_token'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'id' => 'string'
    ];

    protected $table = 'users';

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function accounts(){
        return $this->morphMany(Account::class, 'owner');
    }

    public function hasAnyRole($roles)
    {
        return null !== $this->roles()->whereIn('name', $roles)->first();
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_user', 'user_id', 'role_id')
            ->using('App\Models\Pivots\BasePivot')
            ->withTimestamps();
    }

    public function hasRole($role)
    {
        return null !== $this->roles()->where('name', $role)->first();
    }
}
