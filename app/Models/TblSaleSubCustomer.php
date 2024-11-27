<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblSaleSubCustomer extends Model
{
    protected $table = 'tbl_sale_sub_customer';
    protected $primaryKey = 'sub_customer_id';

    public $timestamps = false;

    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }

    function customer(){
        return $this->belongsTo(TblSaleCustomer::class , 'customer_id');
    }
}
