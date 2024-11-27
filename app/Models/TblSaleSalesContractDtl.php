<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblSaleSalesContractDtl extends Model
{
    protected $table = 'tbl_sale_sales_contract_dtl ';
    protected $primaryKey = 'sales_contract_dtl_id';

    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
    function product(){
        return $this->belongsTo(TblPurcProduct::class, 'product_id');
    }
    function barcode(){
        return $this->belongsTo(TblPurcProductBarcode::class, 'product_barcode_id');
    }
    function uom(){
        return $this->belongsTo(TblDefiUom::class, 'uom_id');
    }
    function packing(){
        return $this->belongsTo(TblPurcPacking::class, 'packing_id');
    }
}
