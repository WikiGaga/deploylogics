<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblServManageSchedule extends Model
{
    protected $table = 'tbl_serv_manage_schedule';

    protected $primaryKey = 'schedule_id';

    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }

    public function dtls(){
        return $this->belongsTo(TblSaleSalesOrder::class , 'request_quotation_id' , 'sales_order_id')
        ->with('dtls','expense','customer','sales_contract','sale_booking','city','area','status');
    }
    public function orderDtls(){
        return $this->belongsTo(TblSaleSalesOrder::class , 'sales_order_id')
        ->with('dtls','expense','customer','sales_contract','sale_booking','city','area','status');
    }

    public function user(){
        return $this->belongsTo(User::class , 'schedule_assign_to' , 'id');
    }

    public function quotation(){
        return $this->belongsTo(TblSaleSalesOrder::class , 'request_quotation_id' , 'sales_order_id');
    }
    public function order(){
        return $this->belongsTo(TblSaleSalesOrder::class , 'sales_order_id');
    }
}
