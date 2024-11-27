<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblSoftReportStyling extends Model
{
    protected $table = 'tbl_soft_report_styling';
    protected $primaryKey = 'report_styling_id';

    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }

    protected $fillable = [
          'report_styling_id',
          'report_id',
          'report_styling_column_no',
          'report_styling_column_type',
          'report_styling_key',
          'report_styling_value'
    ];
}
