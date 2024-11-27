<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblSoftReportingFilterCase extends Model
{
    protected $table = 'tbl_soft_reporting_filter_case';
    protected $primaryKey = 'reporting_filter_case_id';


    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
}
