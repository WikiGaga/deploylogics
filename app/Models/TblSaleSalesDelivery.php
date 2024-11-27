<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblSaleSalesDelivery extends Model
{
    protected $table = 'tbl_sale_sales_delivery';
    protected $primaryKey = 'sales_delivery_id';

    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }

    public function dtls() {
        return $this->hasMany(TblSaleSalesDeliveryDtl::class, 'sales_delivery_id')
            ->with('product','barcode','uom')
            ->orderBy('sr_no','asc');
    }

    public function customer() {
        return $this->belongsTo(TblSaleCustomer::class, 'customer_id');
    }

    function SO(){
        return $this->belongsTo(TblSaleSalesOrder::class, 'sales_order_booking_id');
    }
    function sales_contract(){
        return $this->belongsTo(TblSaleSalesContract::class, 'sales_contract_id');
    }

    function sales_invoice(){
        return $this->belongsTo(TblSaleSales::class, 'sales_id');
    }
}
