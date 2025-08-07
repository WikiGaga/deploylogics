<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Variation extends Model
{
    protected $table = 'variations';

    protected $primaryKey = 'id';

    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }

    public function variationOptions()
    {
        return $this->hasMany(VariationOption::class, 'variation_id');
    }


}
