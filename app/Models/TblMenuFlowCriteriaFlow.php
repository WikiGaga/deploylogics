<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblMenuFlowCriteriaFlow extends Model
{
    protected $table = 'tbl_menu_flow_criteria_flows';
    protected $primaryKey = 'menu_flow_criteria_flow_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'menu_flow_criteria_flow_id',
        'menu_flow_criteria_id',
        'stg_flows_id',
        'flow_order',
        'flow_name',
        'lead_time_value',
        'lead_time_unit',
        'reminder_time_minutes',
        'require_all_users'
    ];

    protected $casts = [
        'flow_order' => 'integer',
        'lead_time_value' => 'integer',
        'reminder_time_minutes' => 'integer',
        'require_all_users' => 'boolean'
    ];

    /**
     * Relationships
     */
    public function criteria()
    {
        return $this->belongsTo(TblMenuFlowCriteria::class, 'menu_flow_criteria_id', 'menu_flow_criteria_id');
    }

    public function actions()
    {
        return $this->hasMany(TblMenuFlowCriteriaFlowAction::class, 'menu_flow_criteria_flow_id', 'menu_flow_criteria_flow_id');
    }

    public function users()
    {
        return $this->hasMany(TblMenuFlowCriteriaFlowUser::class, 'menu_flow_criteria_flow_id', 'menu_flow_criteria_flow_id');
    }

    public function designations()
    {
        return $this->hasMany(TblMenuFlowCriteriaFlowDesignation::class, 'menu_flow_criteria_flow_id', 'menu_flow_criteria_flow_id');
    }

    public function bypasses()
    {
        return $this->hasMany(TblMenuFlowCriteriaFlowBypass::class, 'menu_flow_criteria_flow_id', 'menu_flow_criteria_flow_id');
    }

    public function stagingFlow()
    {
        return $this->belongsTo(TblStgFlows::class, 'stg_flows_id', 'stg_flows_id');
    }
}
