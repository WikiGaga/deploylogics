<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblWhatsAppWords extends Model
{
    protected $table = 'tbl_wa_word';
    protected $primaryKey = 'word_id';

    public $timestamps = false;

    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }

    public function dtl(){
        return $this->hasMany(TblWhatsAppWordsDtl::class , 'word_id');
    }
}
