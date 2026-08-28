<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblWaSalaryNotificationDtl extends Model
{
    protected $table = 'tbl_wa_salary_notification_dtl';
    protected $primaryKey = 'dtl_id';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'dtl_id',
        'batch_id',
        'row_no',
        'employee_name',
        'phone',
        'net_payment',
        'template_params',
        'status',
        'meta_message_id',
        'api_response',
        'message_exception',
        'sent_at',
        'created_at',
    ];

    protected static function primaryKeyName()
    {
        return (new static)->getKeyName();
    }

    public function batch()
    {
        return $this->belongsTo(TblWaSalaryNotificationBatch::class, 'batch_id', 'batch_id');
    }
}
