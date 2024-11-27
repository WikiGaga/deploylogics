<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class TblWhatsAppChat extends Model
{
    protected $table = 'tbl_wa_chat';
    protected $primaryKey = 'chat_id';

    public $timestamps = true;

    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }

    public function contact(){
        return $this->belongsTo(TblWhatsAppContact::class , 'phone_no' , 'phone_no')
        ->withCount('unreadMessages')->with('lastMessage');
    }

    public function user(){
        return $this->belongsTo(User::class , 'user_id' , 'id');
    }
}
