<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblSoftReports extends Model
{
    protected $table = 'tbl_soft_report';
    protected $primaryKey = 'report_id';

    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }

    public function report_user_criteria(){
        return $this->hasMany(TblSoftReportUserCriteria::class,'report_id');
    }

    public function report_styling(){
        return $this->hasMany(TblSoftReportStyling::class,'report_id')->orderBy('report_styling_column_no');
    }

}
