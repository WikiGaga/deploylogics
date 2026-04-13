<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ViewShiftSessions extends Model
{
    protected $table = 'vw_shift_sessions';
    protected $primaryKey = 'session_id';

    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
}
