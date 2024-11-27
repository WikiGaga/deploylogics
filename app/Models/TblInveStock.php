<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblInveStock extends Model
{
    protected $table = 'tbl_inve_stock';
    protected $primaryKey = 'stock_id';

    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
    public function stock_dtls() {
        return $this->hasMany(TblInveStockDtl::class, 'stock_id')
            ->with('product','barcode','uom')
            ->orderBy('stock_dtl_sr_no','asc');
    }

    public function audit_stock_dtls() {
        return $this->hasMany(ViewInveStock::class, 'stock_id');
    }


    function product(){
        return $this->belongsTo(TblPurcProduct::class, 'product_id');
    }
    function barcode(){
        return $this->belongsTo(TblPurcProductBarcode::class, 'product_barcode_id');
    }
    function supplier(){
        return $this->belongsTo(TblPurcSupplier::class, 'supplier_id');
    }
    function formulation(){
        return $this->belongsTo(TblInveItemFormulation::class, 'formulation_id','item_formulation_id');
    }
    function store(){
        return $this->belongsTo(TblDefiStore::class, 'stock_store_from_id','store_id')->select(['store_id','store_name']);
    }
    function location(){
        return $this->belongsTo(ViewInveDisplayLocation::class, 'stock_location_id','display_location_id')->select(['display_location_id','display_location_name_string']);
    }
}
