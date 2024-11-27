<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblWhatsAppGroup extends Model
{
    protected $table = 'tbl_wa_group';
    protected $primaryKey = 'grp_id';

    public $timestamps = false;

    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }

    public function inContactEntry(){
        return $this->hasOne(TblWhatsAppContact::class , 'grp_id')->where('cnt_is_group' , 1);
    }
}
