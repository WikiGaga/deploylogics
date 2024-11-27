<?php

namespace App\Models\Rent;

use Illuminate\Database\Eloquent\Model;

class TblRentAgreementDtl extends Model
{
    protected $table = 'tbl_rent_rent_agreement_dtl';
    protected $primaryKey = 'rent_agreement_dtl_id';


    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
}
