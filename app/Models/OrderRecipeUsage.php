<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderRecipeUsage extends Model
{
    protected $table = 'order_recipe_usages';

    protected $fillable = [
        'order_id',
        'order_detail_id',
        'restaurant_id',
        'food_id',
        'option_list_id',
        'food_recipe_id',
        'product_id',
        'order_quantity',
        'product_quantity',
        'product_quantity_used',
        'measure_unit',
        'usage_date',
    ];

    protected $casts = [
        'usage_date' => 'date',
        'product_quantity' => 'decimal:4',
    ];
}



