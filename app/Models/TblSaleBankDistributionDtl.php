<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblSaleBankDistributionDtl extends Model
{
    protected $table = 'tbl_sale_bank_distribution_dtl';
    protected $primaryKey = 'bd_dtl_id';


    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
}
