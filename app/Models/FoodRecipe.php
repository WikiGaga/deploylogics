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
        'item_formulation_date',
        'item_formulation_remarks'
    ];

    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }

    public function option() {
        return $this->belongsTo(OptionsList::class, 'food_id'); // Keep same foreign key for compatibility
    }
    public function dtls() {
        return $this->hasMany(FoodRecipeDtl::class, 'food_recipe_id');
    }

}
