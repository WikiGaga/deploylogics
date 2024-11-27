<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblServDeliveryCharges extends Model
{
    protected $table = 'tbl_serv_delivery_charges';
    protected $primaryKey = 'delivery_charges_id';

    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }

    public function dtls(){
        return $this->hasMany(TblServDeliveryChargesDtl::class , 'delivery_charges_id')
        ->orderBy('sr_no');
    }
}
