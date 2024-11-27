<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblSoftBusinessNature extends Model
{
    protected $table = 'tbl_soft_business_nature';
    protected $primaryKey = 'business_nature_id';

    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
}
