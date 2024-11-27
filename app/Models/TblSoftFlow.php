<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblSoftFlow extends Model
{
    protected $table = 'tbl_soft_menu_flow';
    protected $primaryKey = 'menu_flow_id';


    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
}
