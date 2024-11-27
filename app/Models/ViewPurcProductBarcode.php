<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ViewPurcProductBarcode extends Model
{
    protected $table = 'vw_purc_product_barcode';
    protected $primaryKey = 'product_id';

    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
}
