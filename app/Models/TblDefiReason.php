<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblDefiReason extends Model
{
    protected $table = 'tbl_defi_reason';
    protected $primaryKey = 'reason_id';

    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
    
}
