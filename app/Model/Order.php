<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    /**
     * sets field that are mass assignable
     *
     * @var array
     */
    protected $fillable = ['property_id', 'fractions_qty', 'price'];

    /**
     * The sets a relationship with users
     *
     * @var array
     */
    public function user()
    {
        return $this->belongsTo('App\User');
    }

    /**
     * The sets a relationship with properties
     *
     * @var array
     */
    public function property()
    {
        return $this->belongsTo('App\Model\Property');
    }

    /**
     * Validation rules.
     *
     * @var array
     */

    public static $createOrderRules = [
        'property_id' => 'required|numeric',
        'fractions_qty' => 'required|numeric|gte:1',
        'price' => 'required|numeric',

    ];
}
