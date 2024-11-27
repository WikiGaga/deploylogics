<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ViewPurcProductBarcodeHelp extends Model
{
    protected $table = 'vw_purc_product_barcode_help';
    protected $primaryKey = 'product_barcode_id';

    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
}
