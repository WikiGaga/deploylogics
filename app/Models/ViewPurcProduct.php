<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ViewPurcProduct extends Model
{
    protected $table = 'vw_purc_product';
    protected $primaryKey = 'product_id';

    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
}
