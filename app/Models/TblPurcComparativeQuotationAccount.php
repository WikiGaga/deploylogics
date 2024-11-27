<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblPurcComparativeQuotationAccount extends Model
{
    protected $table = 'tbl_purc_comparative_quotation_acc';
    protected $primaryKey = 'comparative_quotation_acc_id';

    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
}
