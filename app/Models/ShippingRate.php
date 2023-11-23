<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShippingRate extends Model
{
    use HasFactory;

    protected $fillable = [
        'country_code',
        'rate',
        'currency',
    ];

    protected $primaryKey = 'country_code';
    protected $keyType = 'string';
    public $incrementing = false;

    
}
