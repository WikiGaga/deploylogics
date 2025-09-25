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
        'product_name',
        'quantity',
        'uom_id',
        'product_barcode_id',
        'packing_id',
        'business_id',
        'company_id',
        'branch_id'
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

    public function barcode()
    {
        return $this->belongsTo(TblPurcProductBarcode::class, 'product_barcode_id');
    }

    public function uom()
    {
        return $this->belongsTo(TblDefiUom::class, 'uom_id');
    }

    public function foodRecipe()
    {
        return $this->belongsTo(FoodRecipe::class, 'food_recipe_id');
    }

    // Additional relations for better data access
    public function business() {
        return $this->belongsTo(TblSoftBusiness::class, 'business_id');
    }

    public function company() {
        return $this->belongsTo(TblSoftCompany::class, 'company_id');
    }

    public function branch() {
        return $this->belongsTo(TblSoftBranch::class, 'branch_id');
    }

}
