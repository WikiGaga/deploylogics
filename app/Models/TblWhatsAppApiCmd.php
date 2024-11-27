<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblWhatsAppApiCmd extends Model
{
    protected $table = 'tbl_wa_apicmd';
    protected $primaryKey = 'cmd_id';

    public $timestamps = false;

    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
}
