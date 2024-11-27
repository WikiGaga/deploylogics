<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblSaleSalesOrder extends Model
{
    protected $table = 'tbl_sale_sales_order';
    protected $primaryKey = 'sales_order_id';

    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }

    public function dtls() {
        return $this->hasMany(TblSaleSalesOrderDtl::class, 'sales_order_id')
            ->with('product','barcode','uom','packing')
            ->orderBy('sr_no','asc');
    }
    public function customer() {
        return $this->belongsTo(TblSaleCustomer::class, 'customer_id');
    }
    public function expense() {
        return $this->hasMany(TblSaleSalesOrderExpense::class, 'sales_order_id')->with('accounts');
    }
    function sales_contract(){
        return $this->belongsTo(TblSaleSalesContract::class, 'sales_contract_id');
    }
    function sale_booking(){
        return $this->belongsTo(self::class, 'sales_order_booking_id','sales_order_id')
            ->select(['sales_order_id','sales_order_code']);
    }
    function city(){
        return $this->belongsTo(TblDefiCity::class , 'city_id');
    }
    function area(){
        return $this->belongsTo(TblDefiArea::class , 'area_id');
    }
    function status(){
        return $this->belongsTo(TblDefiOrderStatus::class , 'sales_order_status' , 'order_status_id');
    }
    function quotation(){
        return $this->belongsTo(TblSaleSalesOrder::class , 'sales_quotation_id' , 'sales_order_id');
    }
    function order(){
        return $this->belongsTo(TblSaleSalesOrder::class , 'service_order_id' , 'sales_order_id');
    }
    function user(){
        return $this->belongsTo(User::class , 'sales_order_sales_man' , 'id');
    }
    function serviceSchedule(){
        return $this->hasOne(TblServManageSchedule::class ,'request_quotation_id' , 'sales_order_id')
        ->with('user');
    }
    function orderSchedule(){
        return $this->hasOne(TblServManageSchedule::class ,'sales_order_id' , 'sales_order_id')
        ->with('user');
    }
    function statusServiceSchedule(){
        return $this->hasOne(TblServManageSchedule::class ,'sales_order_id')
        ->with('user');
    }
    function AssignedSalesMan(){
        return $this->belongsTo(User::class , 'assigned_sales_man' , 'id');
    }

    // Eloquent Scopes
    function scopeFilterCities($query, $arr){
        if(count($arr) > 0){
            return $query->whereIn('city_id' , $arr);
        }
    }
    function scopeFilterAreas($query, $arr){
        if(count($arr) > 0){
            return $query->whereIn('area_id' , $arr);
        }
    }
    function scopeFilterSchedule($query, $arr){
        if(count($arr) > 0){
            return $query->whereIn('schedule_status' , $arr);
        }
    }
    function scopeFilterStatus($query, $arr){
        if(count($arr) > 0){
            return $query->whereIn('sales_order_status' , $arr);
        }
    }
    function scopeFilterSalesMan($query, $arr){
        if(count($arr) > 0){
            return $query->whereIn('assigned_sales_man' , $arr);
        }
    }
    function scopeFilterOrderIds($query, $arr){
        if(count($arr) > 0){
            return $query->whereNotIn('sales_order_id' , $arr);
        }
    }
}
