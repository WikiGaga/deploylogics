<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblSoftAction extends Model
{
    protected $table = 'tbl_soft_menu_action';
    protected $primaryKey = 'menu_action_id';


    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
}
