<?php

namespace App\Models;

use App\Library\Utilities;
use Illuminate\Database\Eloquent\Model;

class TblPurcPurchaseOrder extends Model
{
    protected $table = 'tbl_purc_purchase_order';
    protected $primaryKey = 'purchase_order_id';

    protected $fillable = [
        'purchase_order_id',
        'purchase_order_code',
        'purchase_order_id',
        'po_grn_status'
    ];
    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }

    public function po_details(){
        return $this->hasMany(TblPurcPurchaseOrderDtl::class,'purchase_order_id')
        ->with('product','barcode','uom','po')
        ->where(Utilities::currentBCB())
        ->orderBy('purchase_order_dtlsr_no','asc');
    }
    function supplier(){
        return $this->belongsTo(TblPurcSupplier::class, 'supplier_id');
    }
    function lpo(){
        return $this->belongsTo(TblPurcLpo::class, 'lpo_id');
    }
    function comparative_quotation(){
        return $this->belongsTo(TblPurcComparativeQuotation::class, 'comparative_quotation_id');
    }
}
