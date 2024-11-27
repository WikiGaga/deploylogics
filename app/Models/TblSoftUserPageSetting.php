<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblSoftUserPageSetting extends Model
{
    protected $table = 'tbl_soft_user_page_setting';
    protected $primaryKey = 'user_page_setting_id';

    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
}
