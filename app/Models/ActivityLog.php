<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    protected $table = 'activity_logs';

    protected static function primaryKeyName()
    {
        return (new static)->getKeyName();
    }

    public $timestamps = false;

    protected $fillable = [
        'business_id',
        'user_id',
        'branch_id',
        'device_id',
        'app_version',
        'platform',
        'client_log_id',
        'log_type',
        'category',
        'source',
        'message',
        'error_details',
        'stack_trace',
        'status_code',
        'extra_data',
        'event_at',
        'received_at',
    ];

    protected $casts = [
        'extra_data'    => 'array',
        'event_at'      => 'datetime',
        'received_at'   => 'datetime',
        'client_log_id' => 'integer',
        'status_code'   => 'integer',
        'branch_id'     => 'integer',
        'business_id'   => 'integer',
    ];
}
