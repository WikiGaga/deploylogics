<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblSaleBankDistribution extends Model
{
    protected $table = 'tbl_sale_bank_distribution';
    protected $primaryKey = 'bd_id';

    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }

    public function distribution_dtl()
    {
        return $this->hasMany(TblSaleBankDistributionDtl::class,"bd_id");
    }
}
