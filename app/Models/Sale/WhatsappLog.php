<?php

namespace App\Models\Sale;

use Illuminate\Database\Eloquent\Model;

class WhatsappLog extends Model
{
    protected $table = 'whatsapp_log';

    protected $fillable = [
        'user_id',
        'form_name',
        'entry_code',
        'created_at',
    ];
}
