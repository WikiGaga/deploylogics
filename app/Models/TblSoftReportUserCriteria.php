<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblSoftReportUserCriteria extends Model
{
    protected $table = 'tbl_soft_report_user_criteria';
    protected $primaryKey = 'report_user_criteria_id';

    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
}
