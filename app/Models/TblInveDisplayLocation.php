<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblInveDisplayLocation extends Model
{
    protected $table = 'tbl_inve_display_location';
    protected $primaryKey = 'display_location_id';

    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
    public function parent() {
        return $this->hasOne(ViewInveDisplayLocation::class, 'display_location_id');
    }
    public function childs() {
        return $this->hasMany(static::class,'parent_display_location_id','display_location_id') ;
    }
}
