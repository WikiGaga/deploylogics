<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblSaleSales extends Model
{
    protected $table = 'tbl_sale_sales';
    protected $primaryKey = 'sales_id';

    protected $fillable = [
        'posted'
    ];

    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }

    public function dtls() {
        return $this->hasMany(TblSaleSalesDtl::class, 'sales_id')
            ->with('product','barcode','uom','packing')
            ->orderBy('sr_no','asc');
    }

    public function simp_dtls() {
        return $this->hasMany(TblSaleSalesDtl::class, 'sales_id')
            ->select(['sales_dtl_id','sales_id','sales_dtl_vat_amount','sales_dtl_total_amount','sales_dtl_disc_amount','sales_dtl_amount'])
            ->orderBy('sr_no','asc');
    }

    public function expense() {
        return $this->hasMany(TblSaleSalesExpense::class, 'sales_id')->with('accounts');
    }

    public function customer() {
        return $this->belongsTo(TblSaleCustomer::class, 'customer_id');
    }

    public function customer_view() {
        return $this->belongsTo(ViewSaleCustomer::class, 'customer_id');
    }

    public function supplier_view() {
        return $this->belongsTo(ViewPurcSupplier::class, 'customer_id');
    }

    function SO(){
        return $this->belongsTo(TblSaleSalesOrder::class, 'sales_order_booking_id' , 'sales_order_id');
    }
    function sales_contract(){
        return $this->belongsTo(TblSaleSalesContract::class, 'sales_contract_id');
    }

}
