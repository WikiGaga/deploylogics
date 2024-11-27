<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblSchemeAvail extends Model
{
    protected $table = 'tbl_scheme_avail';
    protected $primaryKey = 'scheme_avail_id';

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
