<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblPurcDemand extends Model
{
    protected $table = 'tbl_purc_demand';
    protected $primaryKey = 'demand_id';

    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
    public function dtls(){
        return $this->hasMany(TblPurcDemandDtl::class,'demand_id')
        ->with('product','barcode','uom','packing')
        ->orderBy('sr_no','asc');
    }
    public function salesman(){
        return $this->belongsTo(User::class,'salesman_id');
    }
    public function branch(){
        return $this->belongsTo(TblSoftBranch::class,'branch_id');
    }
    function supplier(){
        return $this->belongsTo(TblPurcSupplier::class, 'supplier_id');
    }

    
}

