<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblAutoDemand extends Model
{
    protected $table = 'tbl_purc_auto_demand';
    protected $primaryKey = 'ad_id';
    
    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }

    function dtl(){
        return $this->hasMany(TblAutoDemandDtl::class , 'ad_id')
        ->with('product','barcode','uom','packing','demand','supplier','branch');
    }

    function criterias(){
        return $this->hasMany(TblPurcAutoDemandCriteria::class , 'ad_id');
    }

    function requestBranchs(){
        return $this->hasMany(TblPurcAutoDemandRequest::class , 'ad_id')
        ->with('product','barcode','uom');
    }

    public function getGroupIdAttribute($value){
        return explode("," , $value);
    }
    public function getSupplierIdAttribute($value){
        return explode("," , $value);
    }
    public function getDemandIdAttribute($value){
        return explode("," , $value);
    }
    public function getLocationIdAttribute($value){
        return explode("," , $value);
    }
    public function getConsumptionBranchIdAttribute($value){
        return explode("," , $value);
    }
    public function getSuggestStockRequestBranchAttribute($value){
        return explode("," , $value);
    }
}
