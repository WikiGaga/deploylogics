<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TempPro extends Model
{
    protected $table = 'TEMP_PRO';
    protected $primaryKey = 'id';

    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
    public function dtl(){
        return $this->hasMany(TempProDtl::class,"product_id");
    }
}
