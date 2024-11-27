<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ViewAllColumnData extends Model
{
    protected $table = 'vw_all_column_data';
  //  protected $primaryKey = 'display_location_id';

    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
}
