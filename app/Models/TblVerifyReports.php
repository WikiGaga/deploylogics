<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblVerifyReports extends Model
{
    protected $table = 'tbl_verify_reports';
    protected $primaryKey = 'verify_reports_id';

    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }

    public function branch(){
        return $this->belongsTo(TblSoftBranch::class , 'branch_id');
    }
}
