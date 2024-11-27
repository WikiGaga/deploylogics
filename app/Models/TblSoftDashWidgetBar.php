<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblSoftDashWidgetBar extends Model
{
    protected $table = 'tbl_soft_dash_widget_bar';
    protected $primaryKey = 'dash_widget_bar_id';


    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
}
