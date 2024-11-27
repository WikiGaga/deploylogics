<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblDefiPaymentType extends Model
{
    protected $table = 'tbl_defi_payment_type';
    protected $primaryKey = 'payment_type_id';


    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
}
