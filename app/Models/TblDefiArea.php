<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblDefiArea extends Model
{
    protected $table = 'tbl_defi_area';
    protected $primaryKey = 'area_id';

    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
    public function city()
    {
        return $this->belongsTo(TblDefiCity::class,'city_id');
    }

    public function dtls(){
        return $this->belongsTo(TblServDeliveryChargesDtl::class , 'area_id' , 'area_id');
    }
}
