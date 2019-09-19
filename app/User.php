<?php

namespace App;

use App\Models\Account;
use App\Models\Branch;
use App\Models\Order;
use App\Models\Position;
use App\Models\Role;
use App\Models\StoredItem;
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
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime'
    ];

    public function position()
    {
        return $this->belongsTo(Position::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function clientRole()
    {
        return $this->belongsToMany(Role::class)->where('name', 'client');
    }

    public function accounts()
    {
        return $this->hasMany(Account::class);
    }

    public function storedItems()
    {
        return $this->hasMany(StoredItem::class, 'owner_id');
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'owner');
    }

    public function registeredOrders()
    {
        return $this->hasMany(Order::class, 'registeredBy');
    }

    //TODO remove from here

    public function managedBranch()
    {
        return $this->hasOne(Branch::class, 'director');
    }

    /**
     * @param string|array $roles
     * @return bool
     */
    public function authorizeRoles($roles)
    {
        if (is_array($roles)) {
            return $this->hasAnyRole($roles) ||
                abort(401, 'This action is unauthorized.');
        }

        return $this->hasRole($roles) ||
            abort(401, 'This action is unauthorized.');
    }

    public function hasAnyRole($roles)
    {
        return null !== $this->roles()->whrerIn('name', $roles)->first();
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class)
            ->using('App\Models\Pivots\BasePivot')
            ->withTimestamps();
    }

    public function hasRole($role)
    {
        return null !== $this->roles()->where('name', $role)->first();
    }
}
