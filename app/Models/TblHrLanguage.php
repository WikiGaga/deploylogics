<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblHrLanguage extends Model
{
    protected $table = 'tbl_payr_language';
    protected $primaryKey = 'language_id';


    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }

    public function employee() {
        return $this->belongsToMany(TblHrEmployee::class,'tbl_payr_language_known','employee_id','language_id');
    }
}
