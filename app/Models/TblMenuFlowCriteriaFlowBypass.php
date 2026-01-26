<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblMenuFlowCriteriaFlowBypass extends Model
{
    protected $table = 'tbl_menu_flow_criteria_flow_bypass';
    protected $primaryKey = 'menu_flow_criteria_flow_bypass_id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'menu_flow_criteria_flow_bypass_id',
        'menu_flow_criteria_flow_id',
        'bypass_type',
        'bypass_user_id',
        'bypass_designation_id'
    ];

    /**
     * Relationships
     */
    public function flow()
    {
        return $this->belongsTo(TblMenuFlowCriteriaFlow::class, 'menu_flow_criteria_flow_id', 'menu_flow_criteria_flow_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'bypass_user_id', 'id');
    }
}
