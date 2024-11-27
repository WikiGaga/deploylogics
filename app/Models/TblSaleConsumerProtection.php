<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblSaleConsumerProtection extends Model
{
    protected $table = 'tbl_sale_consumer_protection';
    protected $primaryKey = 'protection_id';

    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }

    public function dtls() {
        return $this->hasMany(TblSaleConsumerProtectionDtl::class, 'protection_id')
            ->with('product','barcode','uom','packing')
            ->orderBy('sr_no','asc');
    }

    public function expense() {
        return $this->hasMany(TblSaleConsumerProtectionExpense::class, 'protection_id')->with('accounts');
    }

    public function customer() {
        return $this->belongsTo(TblSaleCustomer::class, 'customer_id');
    }

    public function customer_view() {
        return $this->belongsTo(ViewSaleCustomer::class, 'customer_id');
    }
    
    function SO(){
        return $this->belongsTo(TblSaleSalesOrder::class, 'sales_order_booking_id');
    }
    function sales_contract(){
        return $this->belongsTo(TblSaleSalesContract::class, 'sales_contract_id');
    }

}
