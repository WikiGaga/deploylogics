<?php

namespace App\Models\Rent;

use Illuminate\Database\Eloquent\Model;

class TblRentRentLocation extends Model
{
    protected $table = 'tbl_rent_rent_location';
    protected $primaryKey = 'rent_location_id';


    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
}
