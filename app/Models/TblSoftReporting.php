<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblSoftReporting extends Model
{
    protected $table = 'tbl_soft_reporting';
    protected $primaryKey = 'reporting_id';


    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
    public function user_filter(){
        return $this->hasMany(TblSoftReportingUserFilter::class,'reporting_id');
    }

    public function reporting_dimension(){
        return $this->hasMany(TblSoftReportingDimension::class,'reporting_id');
    }
    public function reporting_filter(){
        return $this->hasMany(TblSoftReportingFilter::class,'reporting_id')->with('filter_dtl');
    }
}
