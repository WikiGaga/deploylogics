<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblMenuFlowCriteriaFlowAction extends Model
{
    protected $table = 'tbl_menu_flow_criteria_flow_actions';
    protected $primaryKey = 'menu_flow_criteria_flow_action_id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'menu_flow_criteria_flow_action_id',
        'menu_flow_criteria_flow_id',
        'action_name',
        'send_notification',
        'notification_config'
    ];

    protected $casts = [
        'send_notification' => 'boolean',
        'notification_config' => 'array'
    ];

    /**
     * Relationships
     */
    public function flow()
    {
        return $this->belongsTo(TblMenuFlowCriteriaFlow::class, 'menu_flow_criteria_flow_id', 'menu_flow_criteria_flow_id');
    }
}
