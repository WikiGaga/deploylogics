<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblSoftUserActivityLog extends Model
{
    protected $table = 'tbl_soft_user_activity_log';
    protected $primaryKey = 'user_activity_log_id';
    protected $fillable = [
        'user_activity_log_id',
        'menu_dtl_id',
        'document_id',
        'document_name',
        'activity_form_menu_dtl_id',
        'activity_form_id',
        'activity_form_type',
        'action_type',
        'browser_dtl',
        'ip_address',
        'form_data',
        'remarks',
        'user_id',
        'business_id',
        'company_id',
        'branch_id',
    ];
    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }

    public function user() {
        return $this->belongsTo(User::class, 'user_id','id')->select(['id','name']);
    }
    public function business(){
        return $this->belongsTo(TblSoftBusiness::class,'business_id')->select(['business_id','business_name']);
    }
    public function company(){
        return $this->belongsToMany(TblSoftCompany::class,'company_id')->select(['company_id','company_name']);
    }
    public function branch(){
        return $this->belongsTo(TblSoftBranch::class,'branch_id')->select(['branch_id','branch_name']);
    }
}
