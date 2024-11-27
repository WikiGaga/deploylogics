<?php

namespace App\Models\Draft;

use App\Models\TblDefiUom;
use App\Models\TblPurcProduct;
use App\Models\TblPurcProductBarcode;
use App\Models\TblPurcSupplier;
use Illuminate\Database\Eloquent\Model;

class DraftPurcPurchaseOrderDtl extends Model
{
    protected $table = 'draft_purc_purchase_order_dtl';

    protected $primaryKey = 'purchase_order_dtl_id';

    protected $fillable = [
        'purchase_order_dtl_id',
        'purchase_order_id',
        'purchase_order_dtlsr_no',
        'purchase_order_dtlbarcode',
        'product_id',
        'uom_id',
        'purchase_order_dtlpacking',
        'purchase_order_dtlquantity',
        'purchase_order_dtlfoc_quantity',
        'purchase_order_dtlfc_rate',
        'purchase_order_dtlrate',
        'purchase_order_dtlamount',
        'purchase_order_dtldisc_percent',
        'purchase_order_dtldisc_amount',
        'purchase_order_dtlvat_percent',
        'purchase_order_dtlvat_amount',
        'purchase_order_dtltotal_amount',
        'business_id',
        'company_id',
        'branch_id',
        'purchase_order_dtluser_id',
        'comparative_quotation_id',
        'lpo_id',
        'product_barcode_id',
        'product_barcode_barcode',
        'lpo_dtl_id',
        'purchase_order_dtl_remarks',
        'purchase_order_dtlsale_rate',
        'purchase_order_dtlmrp',
        'purchase_order_dtlfed_perc',
        'purchase_order_dtlspec_disc_perc',
        'purchase_order_dtlspec_disc_amount',
        'purchase_order_dtlnet_tp',
        'purchase_order_dtllast_tp',
        'purchase_order_dtlvend_last_tp',
        'purchase_order_dtlfed_amount',
        'purchase_order_dtlsys_quantity',
        'purchase_order_dtltax_on',
        'purchase_order_dtldisc_on',
        'purchase_order_dtltp_diff',
        'purchase_order_dtlafter_dis_amount',
        'purchase_order_dtlgp_perc',
        'purchase_order_dtlgp_amount',
        'purchase_order_dtlgross_amount',
    ];

    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }

    function product(){
        return $this->belongsTo(TblPurcProduct::class, 'product_id')
            ->select('product_id','product_name');
    }
    function barcode(){
        return $this->belongsTo(TblPurcProductBarcode::class, 'product_barcode_id')
            ->select('product_barcode_id','product_barcode_barcode');
    }
    function uom(){
        return $this->belongsTo(TblDefiUom::class, 'uom_id')
            ->select('uom_id','uom_name');
    }
}
