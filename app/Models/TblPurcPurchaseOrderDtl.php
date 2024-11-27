<?php

namespace App\Models;

use App\Models\Defi\TblDefiConstants;
use App\Models\Defi\TblDefiTaxGroup;
use Illuminate\Database\Eloquent\Model;

class TblPurcPurchaseOrderDtl extends Model
{
    protected $table = 'tbl_purc_purchase_order_dtl';
    protected $primaryKey = 'purchase_order_dtl_id';

    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
    function po(){
        return $this->belongsTo(TblPurcPurchaseOrder::class, 'purchase_order_id');
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
        return $this->belongsTo(TblPurcPacking::class, 'purchase_order_dtlpacking');
    }
    function tax_group(){
        return $this->belongsTo(TblDefiTaxGroup::class, 'tax_group_id');
    }
}
