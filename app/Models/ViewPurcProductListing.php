<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ViewPurcProductListing extends Model
{
    protected $table = 'vw_purc_product_listing';
    protected $primaryKey = 'product_id';

    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
}
