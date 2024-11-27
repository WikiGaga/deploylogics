<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblSoftReportingFilterDtl extends Model
{
    protected $table = 'tbl_soft_reporting_filter_dtl';
    protected $primaryKey = 'reporting_filter_dtl_id';


    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
}
