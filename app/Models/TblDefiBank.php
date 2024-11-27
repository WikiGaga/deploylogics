<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblDefiBank extends Model
{
    protected $table = 'tbl_defi_bank';
    protected $primaryKey = 'bank_id';


    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
}
