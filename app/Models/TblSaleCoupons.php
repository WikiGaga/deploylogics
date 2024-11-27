<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblSaleCoupons extends Model
{
    protected $table = 'tbl_sale_coupons';
    protected $primaryKey = 'coupon_id';

    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }

    function coupon_dtl(){
        return $this->hasMany(TblSaleCouponsDtl::class , 'coupon_id')
        ->with('coupon_benificery');
    }
    function benicifery_dtls(){
        return $this->hasMany(TblSaleCouponsDtlDtl::class , 'coupon_id');
    }

    function customer(){
        return $this->belongsTo(TblSaleCustomer::class , 'customer_id');
    }
}
