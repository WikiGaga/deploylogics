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
        'food_recipe_id',
        'product_id',
        'quantity',
        'uom_id'
    ];

    public $incrementing = true;

    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }

    public function product()
    {
        return $this->belongsTo(TblPurcProduct::class, 'product_id');
    }

    public function barcode()
    {
        return $this->belongsTo(TblPurcProductBarcode::class, 'product_barcode_id');
    }

    public function uom()
    {
        return $this->belongsTo(TblDefiUom::class, 'uom_id');
    }

}
