<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblSoftReportingMetricDtl extends Model
{
    protected $table = 'tbl_soft_reporting_metric_dtl';
    protected $primaryKey = 'reporting_metric_dtl_id';


    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
}
