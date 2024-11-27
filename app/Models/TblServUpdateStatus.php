<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblServUpdateStatus extends Model
{
    protected $table = 'tbl_serv_update_status';
    protected $primaryKey = 'update_status_id';


    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }

    public function dtls() {
        return $this->hasMany(TblSaleSalesOrderDtl::class, 'sales_order_id' , 'order_id')
            ->with('product','barcode','uom','packing')
            ->orderBy('sr_no','asc');
    }

    function quotation(){
        return $this->belongsTo(TblSaleSalesOrder::class , 'quotation_id' , 'sales_order_id')->with('AssignedSalesMan');
    }
    function order(){
        return $this->belongsTo(TblSaleSalesOrder::class , 'order_id' , 'sales_order_id')
        ->with('city','area','customer');
    }
    function schedule(){
        return $this->belongsTo(TblServManageSchedule::class , 'schedule_id','schedule_id');
    }
}
