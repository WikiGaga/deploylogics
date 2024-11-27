<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblWhatsAppMessage extends Model
{
    protected $table = 'tbl_wa_msg';
    protected $primaryKey = 'msg_id';

    public $timestamps = false;

    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }

    public function dtls(){
        return $this->hasMany(TblWhatsAppMessageDtl::class , 'msg_id');
    }
}
