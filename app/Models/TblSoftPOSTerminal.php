<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblSoftPOSTerminal extends Model
{
    protected $table = 'tbl_soft_pos_terminal';
    protected $primaryKey = 'terminal_id';


    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
}
