<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblSaleCouponsDtlDtl extends Model
{
    protected $table = 'tbl_sale_coupons_dtl_dtl';
    protected $primaryKey = 'coupon_dtl_dtl_id';

    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }

    function coupon_detail(){
        return $this->belongsTo(TblSaleCouponsDtl::class , 'coupon_id');
    }
}
