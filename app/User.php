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

class User extends Model implements AuthenticatableContract, AuthorizableContract, JWTSubject, CanResetPasswordContract
{
    use Authenticatable, Authorizable, CanResetPassword, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'firstname', 'lastname', 'email', 'is_verified', 'avatar', 'phone', 'birthday',
        'bvn', 'occupation', 'address', 'public_id'
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
        'email' => 'required|email|unique:users',
        'password' => 'required|min:6|regex:/^[a-zA-Z0-9]{6,22}$/',
    ];

    public static $loginRules = [
        'email' => 'required|email|string',
        'password' => 'required|string'
    ];

    public static $updateProfileRules = [
        'avatar' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        'phone' => 'required|regex:/^[0]\\d{10}$/|min:10',
        'address' => 'max:255',
        'occupation' => 'max:255',
    ];

    public static $createProfileRules = [
        'avatar' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        'phone' => 'required|regex:/^[0]\\d{10}$/|min:10',
        'address' => 'max:255',
        'occupation' => 'max:255',
        'birthday' => 'required|date_format:Y-m-d|before:today',
        'bvn' => 'required|numeric',
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
}
