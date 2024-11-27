<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblSoftPOSSetting extends Model
{
    protected $table = 'tbl_soft_pos_settings';
    protected $primaryKey = 'pos_setting_id';


    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
}
