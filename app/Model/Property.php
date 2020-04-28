<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Property extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'image', 'about', 'brochure', 'location', 'investment_population',
        'net_rental_yield', 'holding_period', 'min_fraction_price', 'max_fraction_price',
        'category_id', 'gallery', 'facility', 'video', 'min_yield', 'max_yield'
    ];

    /**
     * The attributes are casted to array.
     *
     * @var array
     */
    protected $casts = [
        'gallery' => 'array',
        'facility' => 'array',
        'gallery_public_id' => 'array'
    ];

    /**
     * The sets a relationship with categories
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category()
    {
        return $this->belongsTo('App\Model\Category');
    }


    /**
     * The sets a relationship with orders
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function order()
    {
        return $this->hasMany('App\Model\Order');
    }

    /**
     * Validation rules.
     *
     * @var array
     */

    public static $propertyRules = [
        'name' => 'required|string|max:255',
        'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        'about' => 'required|string|min:10|max:300',
        'brochure' => 'file|max:10000|mimetypes:application/pdf',
        'location' => 'required|string|max:255',
        'investment_population' => 'required|numeric',
        'net_rental_yield' => 'required|numeric',
        'min_yield' => 'required|numeric',
        'max_yield' => 'required|numeric',
        'holding_period' => 'required|numeric',
        'min_fraction_price' => 'required|numeric|gte:100000',
        'max_fraction_price' => 'required|numeric|lte:15000000',
        'category_id' => 'required|numeric',
        'gallery' => 'array',
        'gallery.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        'facility' => 'array',
        'video' => 'mimetypes:video/avi,video/mpeg,video/quicktime,video/mp4'
    ];
}
