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
    protected $fillable = ['property_id', 'fractions_qty', 'price', 'yield_period'];

    /**
     * The sets a relationship with users
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('App\User');
    }

    /**
     * The sets a relationship with properties
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
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
        'fractions_qty' => 'required|numeric|gte:1|lte:200',
        'yield_period' => 'required|numeric|gte:1',
        'price' => 'required|numeric',
    ];
}
