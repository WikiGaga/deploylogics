<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblSoftReportingDimension extends Model
{
    protected $table = 'tbl_soft_reporting_dimension';
    protected $primaryKey = 'reporting_dimension_id';


    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
}
