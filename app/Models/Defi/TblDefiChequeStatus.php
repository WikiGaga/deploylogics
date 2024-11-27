<?php

namespace App\Models\Defi;

use Illuminate\Database\Eloquent\Model;

class TblDefiChequeStatus extends Model
{
    protected $table = 'tbl_defi_cheque_status';
    protected $primaryKey = 'cheque_status_id';

    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
}
