<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblWhatsAppWordsDtl extends Model
{
    protected $table = 'tbl_wa_word_dtl';
    protected $primaryKey = 'word_id_dtl';

    public $timestamps = false;

    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
}
