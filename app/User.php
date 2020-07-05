<?php

namespace App;

use App\Data\Userstamps\Userstamps;
use App\Models\Branch;
use App\Models\Role;
use App\Models\Till\Account;
use App\Models\Till\Payment;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Rorecek\Ulid\HasUlid;

/**
 * @property string id
 * @property string code
 */
class User extends Authenticatable
{
    use HasUlid, SoftDeletes, Userstamps;

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

    public function accounts()
    {
        return $this->morphMany(Account::class, 'owner');
    }

    public function incomingPayments()
    {
        return $this->hasMany(Payment::class, 'payee_id');
    }

    public function outgoingPayments()
    {
        return $this->hasMany(Payment::class, 'payer_id');
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
