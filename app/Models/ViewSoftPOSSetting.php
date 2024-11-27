<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ViewSoftPOSSetting extends Model
{
    protected $table = 'vw_soft_pos_settings';
    protected $primaryKey = 'pos_setting_id';


    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
}
