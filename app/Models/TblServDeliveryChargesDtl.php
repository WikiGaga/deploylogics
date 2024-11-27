<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblServDeliveryChargesDtl extends Model
{
    protected $table = 'tbl_serv_delivery_charges_dtl';
    protected $primaryKey = 'delivery_charges_dtl_id';

    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }

    public function city(){
        return $this->belongsTo(TblDefiCity::class , 'city_id');
    }
    public function area(){
        return $this->belongsTo(TblDefiArea::class , 'area_id');
    }

}
