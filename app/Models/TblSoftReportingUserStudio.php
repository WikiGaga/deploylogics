<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblSoftReportingUserStudio extends Model
{
    protected $table = 'tbl_soft_reporting_user_studio';
    protected $primaryKey = 'reporting_user_studio_id';


    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }

    public function user_studio_dtl(){
        return $this->hasMany(TblSoftReportingUserStudioDtl::class,'reporting_user_studio_id')
            ->orderBy('reporting_user_studio_dtl_sr');
    }
}
