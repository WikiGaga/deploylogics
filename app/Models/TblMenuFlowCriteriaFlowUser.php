<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblMenuFlowCriteriaFlowUser extends Model
{
    protected $table = 'tbl_menu_flow_criteria_flow_users';
    protected $primaryKey = 'menu_flow_criteria_flow_user_id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'menu_flow_criteria_flow_user_id',
        'menu_flow_criteria_flow_id',
        'user_id'
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
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
