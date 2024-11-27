<?php

namespace App\Models\Rent;

use App\Models\TblAccCoa;
use Illuminate\Database\Eloquent\Model;

class TblRentPartyProfile extends Model
{
    protected $table = 'tbl_rent_party_profile';
    protected $primaryKey = 'party_profile_id';


    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }

    public function chartAccount(){
        return $this->belongsTo(TblAccCoa::class , 'chart_account_id');
    }
}
