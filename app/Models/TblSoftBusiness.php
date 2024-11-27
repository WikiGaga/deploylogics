<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblSoftBusiness extends Model
{
    protected $table = 'tbl_soft_business';
    protected $primaryKey = 'business_id';

    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
}
