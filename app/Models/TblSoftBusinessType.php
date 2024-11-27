<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblSoftBusinessType extends Model
{
    protected $table = 'tbl_soft_business_type';
    protected $primaryKey = 'business_type_id';

    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
}
