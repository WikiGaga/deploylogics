<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblSaleBarcodePriceTag extends Model
{
    protected $table = 'tbl_sale_barcode_price_tag';
    protected $primaryKey = 'barcode_price_tag_id';

    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }

    function barcode_price_tag_dtl(){
        return $this->hasMany(TblSaleBarcodePriceTagDtl::class, 'barcode_price_tag_id')
            ->with('product','uom');
    }
}
