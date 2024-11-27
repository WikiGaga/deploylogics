<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblDefiGSTCalculation extends Model
{
    protected $table = 'tbl_defi_gst_calculation';
    protected $primaryKey = 'gst_calculation_id';


    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
}
