<?php

namespace App\Models\Draft;

use App\Library\Utilities;
use App\Models\TblPurcPurchaseOrderDtl;
use App\Models\TblPurcSupplier;
use Illuminate\Database\Eloquent\Model;

class DraftPurcPurchaseOrder extends Model
{
    protected $table = 'draft_purc_purchase_order';

    protected $primaryKey = 'purchase_order_id';

    protected $fillable = [
        'comparative_quotation_id',
        'lpo_id',
        'purchase_order_id',
        'purchase_order_entry_date',
        'purchase_order_exchange_rate',
        'purchase_order_credit_days',
        'purchase_order_remarks',
        'business_id',
        'company_id',
        'branch_id',
        'purchase_order_user_id',
        'purchase_order_entry_status',
        'purchase_order_entry_date_time',
        'purchase_order_code',
        'supplier_id',
        'payment_mode_id',
        'currency_id',
        'shipment_mode_id',
        'shipment_provided_id',
        'auto_demand_id',
        'priority_value',
        'purchase_order_delivery_date',
        'po_grn_status',
        'purchase_order_overall_discount',
        'purchase_order_overall_disc_amount',
        'purchase_order_total_items',
        'purchase_order_total_qty',
        'purchase_order_total_amount',
        'purchase_order_total_disc_amount',
        'purchase_order_total_gst_amount',
        'purchase_order_total_fed_amount',
        'purchase_order_total_spec_disc_amount',
        'purchase_order_total_gross_net_amount',
        'purchase_order_total_net_amount',
    ];

    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }

    function supplier(){
        return $this->belongsTo(TblPurcSupplier::class, 'supplier_id')
            ->select('supplier_id','supplier_name');
    }


    public function po_details(){
        return $this->hasMany(DraftPurcPurchaseOrderDtl::class,'purchase_order_id')
            ->with('product','barcode','uom')
            ->where(Utilities::currentBCB())
            ->orderBy('purchase_order_dtlsr_no','asc');
    }

}
