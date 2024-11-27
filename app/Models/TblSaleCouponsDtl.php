<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblSaleCouponsDtl extends Model
{
    protected $table = 'tbl_sale_coupons_dtl';
    protected $primaryKey = 'coupon_dtl_id';

    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }

    function coupon_benificery(){
        return $this->hasMany(TblSaleCouponsDtlDtl::class , 'coupon_dtl_id')->orderBy('coupon_identifier' , 'asc');
    }
}
