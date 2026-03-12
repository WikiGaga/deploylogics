<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblMenuFlowCriteria extends Model
{
    protected $table = 'tbl_menu_flow_criteria';
    protected $primaryKey = 'menu_flow_criteria_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }

    protected $fillable = [
        'menu_flow_criteria_id',
        'menu_flow_criteria_dtl_id',
        'menu_flow_criteria_name',
        'menu_dtl_id',
        'menu_flow_criteria_apply_at',
        'menu_flow_criteria_status',
        'menu_flow_criteria_entry_status',
        'business_id',
        'company_id',
        'branch_id',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'menu_flow_criteria_apply_at' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Relationships
     */
    public function conditions()
    {
        return $this->hasMany(TblMenuFlowCriteriaCondition::class, 'menu_flow_criteria_id', 'menu_flow_criteria_id')
            ->orderBy('condition_sr_number');
    }

    public function flows()
    {
        return $this->hasMany(TblMenuFlowCriteriaFlow::class, 'menu_flow_criteria_id', 'menu_flow_criteria_id')
            ->orderBy('flow_order');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by', 'id');
    }

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->where('menu_flow_criteria_status', 1)
            ->where('menu_flow_criteria_entry_status', 1);
    }

    public function scopeForForm($query, $formTableName)
    {
        $formName = strtolower(trim($formTableName));
        return $query->whereRaw('lower(trim(menu_flow_criteria_name)) = ?', [$formName]);
    }

    public function scopeForBusiness($query, $businessId, $companyId, $branchId)
    {
        return $query->where('business_id', $businessId)
            ->where('company_id', $companyId)
            ->where('branch_id', $branchId);
    }
}
