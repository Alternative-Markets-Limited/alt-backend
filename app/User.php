<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Laravel\Lumen\Auth\Authorizable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use App\Notifications\ResetPassword as ResetPasswordNotification;

class User extends Model implements AuthenticatableContract, AuthorizableContract, JWTSubject, CanResetPasswordContract
{
    use Authenticatable, Authorizable, CanResetPassword, Notifiable;

    public function sendPasswordResetNotification($token)
    {
        // Your your own implementation.
        $this->notify(new ResetPasswordNotification($token));
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'firstname', 'lastname', 'email', 'is_verified', 'avatar', 'phone', 'birthday',
        'bvn', 'occupation', 'address', 'public_id', 'admin', 'referrer_id', 'referral_token'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password',
    ];

    /**
     * Validation rules.
     *
     * @var array
     */

    public static $registrationRules = [
        'firstname' => 'required|string|max:255',
        'lastname' => 'required|string|max:255',
        'email' => 'required|string|unique:users|regex:/^([a-zA-Z0-9_\-\.]+)@([a-zA-Z0-9_\-\.]+)\.([a-zA-Z]{2,5})$/',
        'password' => 'required|min:6|max:22',
    ];

    public static $loginRules = [
        'email' => 'required|string|regex:/^([a-zA-Z0-9_\-\.]+)@([a-zA-Z0-9_\-\.]+)\.([a-zA-Z]{2,5})$/',
        'password' => 'required|string'
    ];

    public static $updateProfileRules = [
        'avatar' => 'image|mimes:jpeg,png,jpg,gif,svg',
        'phone' => 'required|regex:/^[0]\\d{10}$/|min:10',
        'address' => 'string|max:255',
        'occupation' => 'string|max:255',
    ];

    public static $createProfileRules = [
        'avatar' => 'image|mimes:jpeg,png,jpg,gif,svg',
        'phone' => 'required|regex:/^[0]\\d{10}$/|min:10',
        'address' => 'string|max:255',
        'occupation' => 'string|max:255',
        'birthday' => 'required|date_format:Y-m-d|before:today',
        'bvn' => 'required|string',
    ];

    public static $verifyBvnRules = [
        'surname' => 'required',
        'firstname' => 'required',
        'dob' => 'required',
        'bvn' => 'required',
        'callbackURL' => 'required',
    ];


    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['referral_link'];

    /**
     * Get the user's referral link.
     *
     * @return string
     */
    public function getReferralLinkAttribute()
    {
        return $this->referral_link = getenv('WEBSITE_URL') . "/register/ref/{$this->referral_token}";
    }

    /**
     * A user has a referrer
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function referrer()
    {
        return $this->belongsTo('App\User');
    }

    /**
     * A user has many referrals.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function referrals()
    {
        return $this->hasMany('App\User');
    }

    /**
     * The sets a relationship with orders
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function orders()
    {
        return $this->hasMany('App\Model\Order');
    }
}
