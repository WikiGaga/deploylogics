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

    protected $fillable = [
        'id',
        'food_id',
        'recipe_date',
        'notes',
        'business_id',
        'company_id',
        'branch_id'
    ];

    protected $casts = [
        'recipe_date' => 'date',
    ];

    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }

    public function option() {
        return $this->belongsTo(OptionsList::class, 'food_id');
    }

    public function dtls() {
        return $this->hasMany(FoodRecipeDtl::class, 'food_recipe_id');
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
