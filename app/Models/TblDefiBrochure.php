<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblDefiBrochure extends Model
{
    protected $table = 'tbl_defi_brochure';
    protected $primaryKey = 'brochure_id';

    protected static function primaryKeyName()
    {
        return (new static)->getKeyName();
    }

    function brochures_dtl()
    {
        return $this->hasMany(TblDefiBrochureDtl::class, 'brochure_id')
        ->with('barcode','product')
        ->orderBy('sr_no','asc');
    }
    function brochures_dtl2()
    {
        return $this->hasMany(TblDefiBrochureDtl::class, 'brochure_id')
        ->with('barcode2','product2')
        ->orderBy('sr_no','asc');
    }


}
