<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ViewPOSSoftTerminal extends Model
{
    protected $table = 'vw_pos_soft_terminal';
    protected $primaryKey = 'terminal_id';

    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
}
