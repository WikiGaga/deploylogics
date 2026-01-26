<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblMenuFlowCriteriaFlowDesignation extends Model
{
    protected $table = 'tbl_menu_flow_criteria_flow_designations';
    protected $primaryKey = 'menu_flow_criteria_flow_designation_id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'menu_flow_criteria_flow_designation_id',
        'menu_flow_criteria_flow_id',
        'designation_id'
    ];

    /**
     * Relationships
     */
    public function flow()
    {
        return $this->belongsTo(TblMenuFlowCriteriaFlow::class, 'menu_flow_criteria_flow_id', 'menu_flow_criteria_flow_id');
    }

    // Add relationship to designation table if you have one
    // public function designation()
    // {
    //     return $this->belongsTo(TblDesignation::class, 'designation_id', 'id');
    // }
}
