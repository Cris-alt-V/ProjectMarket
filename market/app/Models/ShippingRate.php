<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShippingRate extends Model
{
    protected $table = 'shipping_rates';
    protected $fillable = ['carrier', 'base_cost', 'cost_per_km', 'cost_per_kg'];
}
