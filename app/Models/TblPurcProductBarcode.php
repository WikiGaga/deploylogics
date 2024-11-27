<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblPurcProductBarcode extends Model
{
    protected $table = 'tbl_purc_product_barcode';
    protected $primaryKey = 'product_barcode_id';
    protected $fillable = [
        'product_barcode_user_id',
        'product_barcode_entry_status',
        'product_barcode_barcode',
        'product_id',
        'product_barcode_id',
        'product_barcode_purchase_rate',
        'product_barcode_purchase_rate_base',
        'created_at',
        'updated_at',
        'product_barcode_minimum_profit',
        'uom_id',
        'packing_id',
        'product_image_url',
        'product_barcode_variant',
        'product_barcode_purchase_rate_type',
        'business_id',
        'product_barcode_packing',
        'product_guid',
        'uom_name',
        'base_barcode',
        'product_barcode_weight_apply',
        'special_product',
        'product_barcode_cost_rate',
    ];


    public function uom() {
        return $this->belongsTo(TblDefiUom::class, 'uom_id');
    }
    public function packing() {
        return $this->belongsTo(TblPurcPacking::class, 'packing_id');
    }
    public function sale_rate() {
        return $this->hasMany(TblPurcProductBarcodeSaleRate::class, 'product_barcode_id');
    }
    public function cb_sale_rate() {
        // current_branch_sale_rate
        return $this->hasOne(TblPurcProductBarcodeSaleRate::class, 'product_barcode_id')
            ->where('branch_id',auth()->user()->branch->branch_id)->where('product_category_id',2);
    }
    public function purc_rate() {
        return $this->hasMany(TblPurcProductBarcodePurchRate::class, 'product_barcode_id');
    }
    public function purc_rate_first() {
        return $this->hasOne(TblPurcProductBarcodePurchRate::class, 'product_barcode_id');
    }
    public function color() {
        return $this->hasMany(TblPurcBarcodeColor::class, 'product_barcode_id');
    }
    public function size() {
        return $this->hasMany(TblPurcBarcodeSize::class, 'product_barcode_id');
    }
    public function variant() {
        return $this->hasMany(TblPurcBarcodeTags::class, 'product_barcode_id');
    }
    public function barcode_dtl() {
        return $this->hasMany(TblPurcProductBarcodeDtl::class, 'product_barcode_id')
            ->with('user');
    }
    public function barcode_dtl_branch() {
        return $this->hasMany(TblPurcProductBarcodeDtl::class, 'product_barcode_id')
            ->where('branch_id' , auth()->user()->branch_id)
            ->with('user');
    }
    public function product() {
        return $this->belongsTo(TblPurcProduct::class, 'product_id');
    }

    public function barcode()
    {
        return $this->hasMany(TblDefiBrochureDtl::class, 'product_barcode_id')
        ->with('product','uom');
    }

    public function product_smpl_data() {
        return $this->belongsTo(TblPurcProduct::class, 'product_id')
            ->select('product_id','product_name');
    }
}
