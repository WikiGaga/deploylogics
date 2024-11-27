<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ViewInveDisplayLocation extends Model
{
    protected $table = 'vw_inve_display_location';
    protected $primaryKey = 'display_location_id';

    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
}
