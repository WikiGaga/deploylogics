<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblPurcProductBarcodeDtl extends Model
{
    protected $table = 'tbl_purc_product_barcode_dtl';
    protected $primaryKey = 'product_barcode_dtl_id';

    protected $fillable = [
        'product_barcode_id',
        'product_barcode_dtl_id',
        'product_barcode_shelf_stock_display_location',
        'product_barcode_shelf_stock_location',
        'product_barcode_shelf_stock_sales_man',
        'product_barcode_shelf_stock_max_qty',
        'product_barcode_shelf_stock_min_qty',
        'product_barcode_stock_cons_day',
        'product_barcode_stock_limit_neg_stock',
        'product_barcode_stock_limit_limit_apply',
        'product_barcode_stock_limit_reorder_qty',
        'product_barcode_stock_limit_status',
        'product_barcode_stock_limit_max_qty',
        'product_barcode_stock_limit_min_qty',
        'product_barcode_tax_value',
        'product_barcode_tax_apply',
        'business_id',
        'company_id',
        'branch_id',
        'product_barcode_stock_limit_reorder_point',
        'product_barcode_shelf_stock_reorder_point',
        'product_barcode_shelf_stock_dept_qty',
        'product_barcode_shelf_stock_face_qty',
        'created_at',
        'updated_at'
    ];

    public function user(){
        return $this->belongsTo(User::class,'product_barcode_shelf_stock_sales_man');
    }

    public static function checkBarcodeVatPercStatus($barcode_id,$vat_perc){
        $barcode = TblPurcProductBarcodeDtl::where('product_barcode_id',$barcode_id)
            ->where('branch_id',auth()->user()->branch->branch_id)->first();
        if(!empty($barcode) && isset($barcode->product_barcode_tax_apply) && $barcode->product_barcode_tax_apply == 0){
            $barcode->product_barcode_tax_value = $vat_perc;
            $barcode->product_barcode_tax_apply = 1;
            $barcode->save();
        }
        return true;
    }
    public static function checkBarcodeVatPercStatusR($barcode_id,$vat_perc,$branch_id = null){
        $branch_id = isset($branch_id) ? $branch_id : 1;
        $barcode = TblPurcProductBarcodeDtl::where('product_barcode_id',$barcode_id)
            ->where('branch_id',$branch_id)->first();
        if(!empty($barcode) && isset($barcode->product_barcode_tax_apply) && $barcode->product_barcode_tax_apply == 0){
            $barcode->product_barcode_tax_value = $vat_perc;
            $barcode->product_barcode_tax_apply = 1;
            $barcode->save();
        }
        return true;
    }
}
