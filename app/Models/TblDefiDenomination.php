<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblDefiDenomination extends Model
{
    protected $table = 'tbl_defi_denomination';
    protected $primaryKey = 'denomination_id';


    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
}
