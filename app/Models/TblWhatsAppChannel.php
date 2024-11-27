<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblWhatsAppChannel extends Model
{
    protected $table = 'tbl_wa_channel';
    protected $primaryKey = 'channel_id';

    public $timestamps = false;

    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
}
