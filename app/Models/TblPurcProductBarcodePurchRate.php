<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblPurcProductBarcodePurchRate extends Model
{
    protected $table = 'tbl_purc_product_barcode_purch_rate';
    protected $primaryKey = 'product_barcode_purch_id';

    protected $fillable = [
          'product_barcode_purch_id',
          'product_id',
          'product_barcode_id',
          'branch_id',
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
          'last_disc_perc',
          'mrp',
          'sales_tax_rate',
    ];
    protected $guarded = [
        'created_at',
        'updated_at',
    ];
    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }





}
