<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblMenuFlowCriteriaCondition extends Model
{
    protected $table = 'tbl_menu_flow_criteria_conditions';
    protected $primaryKey = 'menu_flow_criteria_condition_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'menu_flow_criteria_condition_id',
        'menu_flow_criteria_id',
        'condition_sr_number',
        'condition_field',
        'condition_operator',
        'condition_value',
        'condition_logic_operator'
    ];

    /**
     * Relationships
     */
    public function criteria()
    {
        return $this->belongsTo(TblMenuFlowCriteria::class, 'menu_flow_criteria_id', 'menu_flow_criteria_id');
    }
}
