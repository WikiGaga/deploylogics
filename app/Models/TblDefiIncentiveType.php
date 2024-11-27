<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblDefiIncentiveType extends Model
{
    protected $table = 'tbl_defi_incentive_type';
    protected $primaryKey = 'incentive_type_id';


    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
}
