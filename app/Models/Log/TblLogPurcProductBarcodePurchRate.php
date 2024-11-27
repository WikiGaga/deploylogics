<?php

namespace App\Models\Log;

use Illuminate\Database\Eloquent\Model;

class TblLogPurcProductBarcodePurchRate extends Model
{
    protected $table = 'tbl_log_purc_product_barcode_purch_rate';
    protected $primaryKey = 'log_product_barcode_purch_id';
    protected $fillable = [
        'log_product_barcode_purch_id',
        'product_barcode_purch_id',
        'product_id',
        'product_barcode_id',
        'branch_id',
        'created_at',
        'updated_at',
        'product_barcode_purchase_rate',
        'product_barcode_cost_rate',
        'product_barcode_avg_rate',
        'product_barcode_barcode',
        'company_id',
        'business_id',
        'sale_rate',
        'tax_rate',
        'inclusive_tax_price',
        'gp_perc',
        'gp_amount',
        'hs_code',
        'tax_group_id',
        'gst_calculation_id',
        'whole_sale_rate',
        'mrp',
        'product_barcode_minimum_profit',
        'last_tp',
        'supplier_last_tp',
        'last_gst_perc',
        'net_tp',
        'browser_dtl',
        'user_ip',
        'activity_form_type',
        'activity_form_action',
        'user_id',
        'old_sale_rate',
        'old_net_tp',
        'old_created_date',
        'document_id',
    ];
    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
}
