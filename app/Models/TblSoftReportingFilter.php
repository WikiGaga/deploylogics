<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblSoftReportingFilter extends Model
{
    protected $table = 'tbl_soft_reporting_filter';
    protected $primaryKey = 'reporting_filter_id';


    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
    public function filter_dtl()
    {
        return $this->hasMany(TblSoftReportingFilterDtl::class, 'reporting_filter_id')
            ->orderBy('reporting_filter_sr_no');
    }
}
