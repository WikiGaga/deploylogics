<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblStgFormLog extends Model
{
    protected $table = 'tbl_stg_form_log';
    protected $primaryKey = 'stg_form_log_id';

    protected $fillable = ['stg_form_log_id','menu_dtl_id','document_id','stg_form_cases_id','user_id','stg_flows_id','stg_actions_id','remarks','posted','stg_form_log_entry_status','business_id','company_id','branch_id'];


    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }

    public function action_btn_dtl() {
        return $this->hasOne(TblStgActions::class,'stg_actions_id','stg_actions_id');
    }

    public function criteria_action() {
        return $this->belongsTo(TblMenuFlowCriteriaFlowAction::class, 'stg_actions_id', 'menu_flow_criteria_flow_action_id');
    }

    public function flow_dtl() {
        return $this->hasOne(TblStgFlows::class,'stg_flows_id','stg_flows_id');
    }

    public function flow_criteria_flow() {
        return $this->hasOne(TblMenuFlowCriteriaFlow::class, 'stg_flows_id', 'stg_flows_id');
    }

    public function user() {
        return $this->hasOne(User::class,'id','user_id');
    }

}
