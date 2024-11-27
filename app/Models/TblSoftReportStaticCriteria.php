<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblSoftReportStaticCriteria extends Model
{
    protected $table = 'tbl_soft_report_static_criteria';
    protected $primaryKey = 'report_static_criteria_id';

    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
}
