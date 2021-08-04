<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Laratrust\Traits\LaratrustUserTrait;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use LaratrustUserTrait;
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'name',
        'username',
        'phone',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function customers()
    {
        return $this->hasMany(Customer::class, 'relating_officer', 'id');
    }
    public function subInventories()
    {
        return $this->hasMany(SubInventory::class, 'staff_id', 'id');
    }
    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'field_staff', 'id');
    }
    public function visits()
    {
        return $this->hasMany(Visit::class, 'visitor', 'id');
    }
    public function geolocation()
    {
        return $this->hasOne(UserGeolocation::class, 'user_id', 'id');
    }
    public function mySchedules()
    {
        return $this->hasMany(Schedule::class, 'rep', 'id');
    }

    public function isSuperAdmin(): bool
    {
        foreach ($this->roles as $role) {
            if ($role->isSuperAdmin()) {
                return true;
            }
        }

        return false;
    }
}
