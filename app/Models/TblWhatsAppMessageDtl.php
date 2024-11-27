<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblWhatsAppMessageDtl extends Model
{
    protected $table = 'tbl_wa_msg_dtl';
    protected $primaryKey = 'msg_id_dtl';

    public $timestamps = false;

    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }

    function message(){
        return $this->belongsTo(TblWhatsAppMessage::class , 'msg_id');
    }
}
