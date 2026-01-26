<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TBLChequeLayouts extends Model
{
    protected $table = 'tbl_cheque_layouts';
    protected $primaryKey = 'id';


    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
}
