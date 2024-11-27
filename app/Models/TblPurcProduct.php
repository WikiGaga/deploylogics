<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblPurcProduct extends Model
{
    protected $table = 'tbl_purc_product';
    protected $primaryKey = 'product_id';

    protected $fillable = ['product_id','product_code','product_name','product_arabic_name','group_item_id','product_brand_id','product_item_type','product_perishable','product_entry_status','business_id','company_id','branch_id','product_user_id'];

    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
    public function product_life(){
        return $this->hasMany(TblPurcProductLife::class, 'product_id')
                    ->with('country');
    }
    public function product_barcode() {
        return $this->hasMany(TblPurcProductBarcode::class, 'product_id')
                    ->with('uom','packing','sale_rate','purc_rate','barcode_dtl','color','size','variant')
                    ->orderBy('product_barcode_sr_no','asc');
    }

    public function specification_tags() {
        return $this->hasMany(TblPurcProductSpecificationTag::class, 'product_id');
    }

    public function item_tags() {
        return $this->hasMany(TblPurcProductItemTag::class, 'product_id');
    }
    public function product_foc() {
        return $this->hasMany(TblPurcProductFOC::class, 'product_id');
    }
    public function group_item(){
        return $this->belongsTo(ViewPurcGroupItem::class,'group_item_id')->select(['group_item_id','group_item_name_string']);
    }
    public function supplier(){
        return $this->belongsTo(TblPurcSupplier::class , 'supplier_id');
    }
}
