<?php

namespace App\Models\Rent;

use Illuminate\Database\Eloquent\Model;

class TblRentAgreement extends Model
{
    protected $table = 'tbl_rent_rent_agreement';
    protected $primaryKey = 'rent_agreement_id';


    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }

    public function dtls(){
        return $this->hasMany(TblRentAgreementDtl::class , 'rent_agreement_id')->orderBy('sr_no');
    }

    public function location(){
        return $this->belongsTo(TblRentRentLocation::class , 'rent_location_id' , 'rent_location_id');
    }

    public function firstParty(){
        return $this->belongsTo(TblRentPartyProfile::class , 'first_party_id' , 'party_profile_id');
    }
    public function secondParty(){
        return $this->belongsTo(TblRentPartyProfile::class , 'second_party_id' , 'party_profile_id');
    }
}
