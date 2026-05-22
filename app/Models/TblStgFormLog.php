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

    public static function stripStagingLogMetaFromRemarks(?string $remarks): string
    {
        if ($remarks === null || $remarks === '') {
            return '';
        }
        return trim(preg_replace('/\r?\n?\[STG_LOG_META\].*$/s', '', $remarks));
    }

    public function getParsedLogMetaAttribute(): array
    {
        if (empty($this->remarks)) {
            return [];
        }
        if (preg_match('/\[STG_LOG_META\](.+)$/s', $this->remarks, $matches)) {
            $decoded = json_decode(trim($matches[1]), true);
            return is_array($decoded) ? $decoded : [];
        }
        return [];
    }

    public function getDisplayUserRemarksAttribute(): ?string
    {
        $text = self::stripStagingLogMetaFromRemarks($this->remarks);
        return $text !== '' ? $text : null;
    }

    public function getDisplayFlowNameAttribute(): string
    {
        $meta = $this->parsed_log_meta;
        if (!empty($meta['flow'])) {
            return (string) $meta['flow'];
        }
        $flow = $this->flow_dtl;
        if ($flow && !empty($flow->stg_flows_name)) {
            return $flow->stg_flows_name;
        }
        return $this->stg_flows_id ? 'Stage ' . $this->stg_flows_id : 'Unknown';
    }

    public function getDisplayActionNameAttribute(): string
    {
        $meta = $this->parsed_log_meta;
        if (!empty($meta['action'])) {
            return (string) $meta['action'];
        }
        $criteriaName = optional($this->criteria_action)->action_name;
        if ($criteriaName) {
            return self::formatLegacyActionName($criteriaName);
        }
        $legacyName = optional($this->action_btn_dtl)->stg_actions_name;
        if ($legacyName) {
            return self::formatLegacyActionName($legacyName);
        }
        return 'Action';
    }

    public function getDisplayActionCodeAttribute(): string
    {
        $meta = $this->parsed_log_meta;
        if (!empty($meta['code'])) {
            return strtolower((string) $meta['code']);
        }
        $criteriaName = optional($this->criteria_action)->action_name;
        if ($criteriaName) {
            return strtolower((string) $criteriaName);
        }
        $legacyName = optional($this->action_btn_dtl)->stg_actions_name;
        if ($legacyName) {
            return strtolower((string) $legacyName);
        }
        return '';
    }

    public static function formatLegacyActionName(string $name): string
    {
        $code = strtolower(trim($name));
        $labels = [
            'save' => 'Update',
            'create' => 'Create',
            'edit' => 'Update',
            'update' => 'Update',
            'forward' => 'Forward',
            'post' => 'Post',
            'back' => 'Back',
            'cancel' => 'Cancel',
            'un_post' => 'Unpost',
            'unpost' => 'Unpost',
        ];
        return $labels[$code] ?? ucfirst(str_replace('_', ' ', $code));
    }

    public function user() {
        return $this->hasOne(User::class,'id','user_id');
    }

}
