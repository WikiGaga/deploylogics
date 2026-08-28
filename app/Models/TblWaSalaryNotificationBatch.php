<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblWaSalaryNotificationBatch extends Model
{
    protected $table = 'tbl_wa_salary_notification_batch';
    protected $primaryKey = 'batch_id';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'batch_id',
        'pay_period',
        'file_name',
        'template_name',
        'template_lang',
        'total_rows',
        'queued_count',
        'sent_count',
        'failed_count',
        'status',
        'user_id',
        'business_id',
        'company_id',
        'branch_id',
        'created_at',
        'completed_at',
    ];

    protected static function primaryKeyName()
    {
        return (new static)->getKeyName();
    }

    public function details()
    {
        return $this->hasMany(TblWaSalaryNotificationDtl::class, 'batch_id', 'batch_id')
            ->orderBy('row_no');
    }
}
