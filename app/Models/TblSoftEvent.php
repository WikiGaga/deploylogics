<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblSoftEvent extends Model
{
    protected $table = 'tbl_soft_menu_events';
    protected $primaryKey = 'menu_event_id';


    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
}
