<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class TblWhatsAppContact extends Model
{
    protected $table = 'tbl_wa_contact';
    protected $primaryKey = 'cnt_id';

    public $timestamps = false;

    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }


    public function group(){
        return $this->belongsTo(TblWhatsAppGroup::class , 'grp_id','grp_id');
    }

    public function city_country(){
        return $this->belongsTo(TblDefiCountry::class , 'country_id');
    }

    public function chat(){
        return $this->hasMany(TblWhatsAppChat::class , 'phone_no' , 'phone_no');
    }
    
    public function lastMessage(){
        return $this->hasOne(TblWhatsAppChat::class , 'phone_no' , 'phone_no')
        ->select('receive_at')
        ->latest();
    }

    public function unreadMessages(){
        return $this->hasMany(TblWhatsAppChat::class , 'phone_no' , 'phone_no')
        ->where('message_status', 'unread');
    }
    
    public function groups(){
        return $this->hasMany(TblWhatsAppGroupContacts::class , 'phone_no','phone_no');
    }
}
