<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblStgFormLog extends Model
{
    protected $table = 'tbl_stg_form_log';
    protected $primaryKey = 'stg_form_log_id';

    protected $fillable = ['stg_form_log_id','menu_dtl_id','form_id','stg_form_cases_id','user_id','stg_flows_id','stg_actions_id','stg_form_log_entry_status','stg_form_log_user_id','business_id','company_id','branch_id'];


    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }

    public function action_btn_dtl() {
        return $this->hasOne(TblStgActions::class,'stg_actions_id','stg_actions_id');
    }
    public function flow_dtl() {
        return $this->hasOne(TblStgFlows::class,'stg_flows_id','stg_flows_id');
    }
    public function user() {
        return $this->hasOne(User::class,'id','user_id');
    }

}
