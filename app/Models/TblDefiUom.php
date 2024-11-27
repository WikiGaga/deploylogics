<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblDefiUom extends Model
{
    protected $table = 'tbl_defi_uom';
    protected $primaryKey = 'uom_id';


    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
}
