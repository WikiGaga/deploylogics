<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class FoodRecipeDtl extends Model
{
    protected $table = 'food_recipes_detail';

    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'food_recipe_id',
        'product_id',
        'quantity',
        'uom_id',
        'packing_id'
    ];

    protected $casts = [
        'quantity' => 'decimal:3',
    ];

    public $incrementing = true;

    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }

    public function product()
    {
        return $this->belongsTo(TblPurcProduct::class, 'product_id');
    }

    public function uom()
    {
        return $this->belongsTo(TblDefiUom::class, 'uom_id');
    }

    public function packing()
    {
        return $this->belongsTo(TblPurcPacking::class, 'packing_id');
    }

    public function foodRecipe()
    {
        return $this->belongsTo(FoodRecipe::class, 'food_recipe_id');
    }

}
