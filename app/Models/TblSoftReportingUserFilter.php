<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblSoftReportingUserFilter extends Model
{
    protected $table = 'tbl_soft_reporting_user_filter';
    protected $primaryKey = 'reporting_user_filter_id';


    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
}
