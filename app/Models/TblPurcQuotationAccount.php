<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblPurcQuotationAccount extends Model
{
    protected $table = 'tbl_purc_quotation_acc';
    protected $primaryKey = 'quotation_acc_id';

    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
}
