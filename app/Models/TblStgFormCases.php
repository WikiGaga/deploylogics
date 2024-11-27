<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblStgFormCases extends Model
{
    protected $table = 'tbl_stg_form_cases';
    protected $primaryKey = 'stg_form_cases_id';

    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
    public function form_flows() {
        return $this->hasMany(TblStgFormFlows::class,'stg_form_cases_id')->with('dtl');
    }
}
