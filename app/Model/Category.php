<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{

    /**
     * sets field that are mass assignable
     *
     * @var array
     */
    protected $fillable = ['name'];

    /**
     * The sets a relationship with properties
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function property()
    {
        return $this->hasMany('App\Model\Property');
    }

    /**
     * Validation rules.
     *
     * @var array
     */

    public static $categoryRules = [
        'name' => 'required|string|max:255',
    ];
}
