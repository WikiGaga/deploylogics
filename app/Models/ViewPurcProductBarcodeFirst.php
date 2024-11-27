<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ViewPurcProductBarcodeFirst extends Model
{
    protected $table = 'vw_purc_product_barcode_first';
    protected $primaryKey = 'product_id';

    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
}
