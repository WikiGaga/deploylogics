<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblDefiBrochureDtl extends Model
{
    protected $table = 'tbl_defi_brochure_dtl';
    protected $primaryKey = 'brochure_dtl_id';

    protected static function primaryKeyName()
    {
        return (new static)->getKeyName();
    }
    function brochure()
    {
        return $this->belongsTo(TblDefiBrochure::class, 'brochure_id');

    }
    function barcode()
    {
        return $this->belongsTo(TblPurcProductBarcode::class, 'product_barcode_id');
    }
    function product()
    {
        return $this->belongsTo(TblPurcProduct::class, 'product_id');
    }
    function barcode2()
    {
        return $this->belongsTo(TblPurcProductBarcode::class, 'product_barcode_id')
            ->select(['product_barcode_id','product_image_url']);
    }
    function product2()
    {
        return $this->belongsTo(TblPurcProduct::class, 'product_id')
            ->select(['product_id','product_arabic_name','product_name']);
    }
}
