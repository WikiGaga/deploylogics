<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblPurcChangeRateDtl extends Model
{
    protected $table = 'tbl_purc_change_rate_dtl';
    protected $primaryKey = 'change_rate_dtl_id';

    protected $fillable = [
        'change_rate_dtl_id',
        'change_rate_id',
        'product_id',
        'product_barcode_id',
        'product_barcode_barcode',
        'change_rate_dtl_old_rate',
        'change_rate_dtl_new_rate',
        'change_rate_dtl_diff',
        'created_at',
        'updated_at',
        'uom_id',
        'change_rate_dtl_packing',
        'old_current_tp',
        'old_last_tp',
        'old_sale_rate',
        'old_gp_amount',
        'old_gp_perc',
        'old_mrp',
        'old_whole_sale_rate',
        'current_tp',
        'sale_rate',
        'gp_amount',
        'gp_perc',
        'mrp',
        'whole_sale_rate'
    ];

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
}
