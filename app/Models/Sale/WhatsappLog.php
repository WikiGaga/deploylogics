<?php

namespace App\Models\Sale;

use Illuminate\Database\Eloquent\Model;

class WhatsappLog extends Model
{
    protected $table = 'whatsapp_log';

    protected $primaryKey = 'whatsapp_log_id';

    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'form_name',
        'entry_code',
        'created_at',
    ];
}
