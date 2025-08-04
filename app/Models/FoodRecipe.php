<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class FoodRecipe extends Model
{
    protected $table = 'food_recipes';

    protected $primaryKey = 'id';

    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }

    // protected $casts = [
    //     'tax' => 'float',
    //     'price' => 'float',
    //     'status' => 'integer',
    //     'discount' => 'float',
    //     'avg_rating' => 'float',
    //     'set_menu' => 'integer',
    //     'category_id' => 'integer',
    //     'restaurant_id' => 'integer',
    //     'reviews_count' => 'integer',
    //     'created_at' => 'datetime',
    //     'updated_at' => 'datetime',
    //     'veg' => 'integer',
    //     'min' => 'integer',
    //     'max' => 'integer',
    //     'maximum_cart_quantity' => 'integer',
    //     'recommended' => 'integer',
    //     'order_count'=>'integer',
    //     'rating_count'=>'integer',
    //     'is_halal'=>'integer',
    // ];



}
