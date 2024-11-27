<?php

namespace App\Models\Rent;

use Illuminate\Database\Eloquent\Model;

class ViewRentRentLocation extends Model
{
    protected $table = 'vw_rent_rent_location';
    protected $primaryKey = 'rent_location_id';

    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
}
