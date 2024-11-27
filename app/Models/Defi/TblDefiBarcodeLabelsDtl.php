<?php

namespace App\Models\Defi;

use App\Models\TblPurcProductBarcode;
use Illuminate\Database\Eloquent\Model;

class TblDefiBarcodeLabelsDtl extends Model
{
    protected $table = 'tbl_defi_barcode_labels_dtl';
    protected $primaryKey = 'barcode_labels_dtl_id';
    protected $fillable = [
        'barcode_labels_dtl_id',
        'barcode_labels_id',
        'product_id',
        'product_barcode_id',
        'product_barcode_barcode',
        'product_name',
        'product_arabic_name',
        'product_image_url',
        'barcode_labels_dtl_rate',
        'barcode_labels_dtl_qty',
        'barcode_labels_dtl_packing_date',
        'barcode_labels_dtl_expiry_date',
        'created_at',
        'updated_at',
        'barcode_labels_dtl_vat',
        'barcode_labels_dtl_vat_per',
        'barcode_labels_dtl_grs_amt',
        'barcode_labels_dtl_amount',
        'barcode_labels_dtl_disc_per',
        'barcode_labels_dtl_disc_amt',
        'group_item_id',
	    'group_item_parent_id',
	    'group_item_name',
	    'group_item_parent_name',
	    'barcode_labels_dtl_weight'
    ];
    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
    function barcode(){
        return $this->belongsTo(TblPurcProductBarcode::class, 'product_barcode_id');
    }
}
