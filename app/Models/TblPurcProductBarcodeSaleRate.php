<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblPurcProductBarcodeSaleRate extends Model
{
    protected $table = 'tbl_purc_product_barcode_sale_rate';
    protected $primaryKey = 'product_barcode_sale_rate_id';
    protected $fillable = ['product_barcode_sale_rate_id','product_barcode_id','branch_id','product_category_id','product_barcode_sale_rate_rate','product_barcode_barcode'];

}
