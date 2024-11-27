<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblPurcBarcodeColor extends Model
{
    protected $table = 'tbl_purc_barcode_color';
    protected $primaryKey = 'barcode_color_id';

    protected $fillable = [
        'barcode_color_id',
        'color_id',
        'product_barcode_id',
        'barcode_color_entry_status',
        'business_id',
        'company_id',
        'branch_id',
        'barcode_color_user_id',
    ];

    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
}
