<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblDefiCountry extends Model
{
    protected $table = 'tbl_defi_country';
    protected $primaryKey = 'country_id';

    protected $fillable = [
        'country_id',
        'country_name',
        'country_entry_status',
        'country_user_id',
        'business_id',
        'company_id',
        'branch_id'
    ];

    public function country_cities(){
        return $this->hasMany('\App\Models\TblDefiCity','country_id')
                ->where('city_entry_status',1)
                ->orderBy('city_name','asc');
    }
    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
}
