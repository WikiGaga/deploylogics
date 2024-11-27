<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ViewDefiCity extends Model
{
    protected $table = 'vw_defi_city';
    protected $primaryKey = 'city_id';


    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
}
