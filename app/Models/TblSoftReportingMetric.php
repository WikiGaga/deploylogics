<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblSoftReportingMetric extends Model
{
    protected $table = 'tbl_soft_reporting_metric';
    protected $primaryKey = 'reporting_metric_id';


    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
}
