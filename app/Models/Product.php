<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'item',
        'price',
        'category_id',
        'country_code',
        'weight',
    ];

    public function shippingRate(): BelongsTo
    {
        return $this->belongsTo(ShippingRate::class, 'country_code', 'country_code');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}
