<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblSoftVoucherSquence extends Model
{
    protected $table = 'tbl_soft_voucher_squence';
    protected $primaryKey = 'squence_id';

    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
 
}
