<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblWhatsAppApiCmdParameter extends Model
{
    protected $table = 'tbl_wa_apicmd_parameter';
    protected $primaryKey = 'par_id';

    public $timestamps = false;

    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
}
