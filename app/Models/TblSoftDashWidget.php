<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblSoftDashWidget extends Model
{
    protected $table = 'tbl_soft_dash_widget';
    protected $primaryKey = 'dash_widget_id';

    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }

    function badgeDtl(){
        return $this->hasMany(TblSoftDashWidgetBadge::class, 'dash_widget_id');
    }
}
