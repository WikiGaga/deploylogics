<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ViewPurcProductBarcodeRate extends Model
{
    protected $table = 'vw_purc_product_barcode_rate';
    protected $primaryKey = 'product_id';

    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
}
