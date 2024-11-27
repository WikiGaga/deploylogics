<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblSoftReportingUserStudioDtl extends Model
{
    protected $table = 'tbl_soft_reporting_user_studio_dtl';
    protected $primaryKey = 'reporting_user_studio_dtl_id';


    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
}
