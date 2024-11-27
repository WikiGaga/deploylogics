<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblWhatsAppGroupContacts extends Model
{
    protected $table = 'tbl_wa_group_contacts';
    protected $primaryKey = 'group_contact_id';

    protected $fillable = ['group_contact_id','phone_no','grp_id'];   

    public $timestamps = true;

    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }

    public function contact(){
        return $this->belongsTo(TblWhatsAppContact::class , 'phone_no', 'phone_no');
    }
}
