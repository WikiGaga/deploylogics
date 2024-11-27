<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblWhatsAppLocations extends Model
{
    protected $table = 'tbl_wa_locations';
    protected $primaryKey = 'wa_location_id';

    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }

}
