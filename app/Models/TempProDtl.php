<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TempProDtl extends Model
{
    protected $table = 'TEMP_PRO_DTL';
    protected $primaryKey = 'id';

    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
}
