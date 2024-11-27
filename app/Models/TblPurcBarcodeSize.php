<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblPurcBarcodeSize extends Model
{
    protected $table = 'tbl_purc_barcode_size';
    protected $primaryKey = 'barcode_size_id';

    protected $fillable = [
        'barcode_size_id',
        'size_id',
        'product_barcode_id',
        'barcode_size_entry_status',
        'business_id',
        'company_id',
        'branch_id',
        'barcode_size_user_id',
    ];

    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
}
