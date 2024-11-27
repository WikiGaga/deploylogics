<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblHrEmployee extends Model
{
    protected $table = 'tbl_payr_employee';
    protected $primaryKey = 'employee_id';


    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }

    public function getEmployeeImgAttribute($value) {
        $path = '/images/employee/'.$value;
        return !empty($value)?asset($path):'';
    }

    public function language() {
        return $this->belongsToMany(TblHrLanguage::class,'tbl_payr_language_known','employee_id','language_id','employee_id','language_id','App\Models\TblHrLanguage');
    }
    public function educational() {
        return $this->hasMany(TblHrEmployeeEducational::class,'employee_id')->orderBy('employee_educational_sr_no');
    }
    public function employment() {
        return $this->hasMany(TblHrEmployeeEmployment::class,'employee_id')->orderBy('employee_employment_sr_no');
    }
    public function insurance() {
        return $this->hasMany(TblHrEmployeeInsurance::class,'employee_id')->orderBy('employee_insurance_sr_no');
    }
    public function bank() {
        return $this->hasMany(TblHrEmployeeBank::class,'employee_id')->orderBy('employee_bank_sr_no');
    }
    public function experience() {
        return $this->hasMany(TblHrEmployeeExperience::class,'employee_id')->orderBy('employee_experience_sr_no');
    }
}
