<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblSchemeSlabDtl extends Model
{
    protected $table = 'tbl_scheme_slab_dtl';
    protected $primaryKey = 'slab_dtl_id';

    public $timestamps = false;

    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }

    function scheme(){
        return $this->belongsTo(TblScheme::class , 'scheme_id');
    }

    function product(){
        return $this->belongsTo(TblPurcProduct::class, 'product_id');
    }
    
    function barcode(){
        return $this->belongsTo(TblPurcProductBarcode::class, 'product_barcode_id')
        ->with('uom');
    }
}
