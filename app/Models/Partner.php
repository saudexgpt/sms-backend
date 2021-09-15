<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Auth;
use App\Notifications\PartnerResetPasswordNotification;
use Illuminate\Database\Eloquent\Model;

class Partner extends Authenticatable
{
    //
    use Notifiable;

    protected $table = 'partners';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
         'partner_username', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];
    
    /**
     * Encrypt password
     * @param hashed $value
     */
    protected function setPasswordAttribute($value) {
        $this->attributes['password'] = bcrypt($value);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     *The return value is in array form and a foreach loop should be used to fetch each element
     */
    public function partnerSchools() 
    {
        return $this->hasMany(PartnerSchool::class);
    }
    
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     *The return value is in array form and a foreach loop should be used to fetch each element
     */
    public function partnerEarnings() 
    {
        return $this->hasMany(PartnerEarning::class);
    }
    
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     *The return value is in array form and a foreach loop should be used to fetch each element
     */
    public function schoolProposals() 
    {
        return $this->hasMany(SchoolProposal::class);
    }
    
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     *The return value is in array form and a foreach loop should be used to fetch each element
     */
    public function subPartner() 
    {
        return $this->hasMany(SubPartner::class);
    }
    
    public function referrer() 
    {
        return $this->belongsTo(SubPartner::class, 'child_partner_id', 'id');
    }
    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new PartnerResetPasswordNotification($token));
    }
}
