<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblDefiCity extends Model
{
    protected $table = 'tbl_defi_city';
    protected $primaryKey = 'city_id';

    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
    public function city_country()
    {
        return $this->belongsTo(TblDefiCountry::class,'country_id');
    }
}
