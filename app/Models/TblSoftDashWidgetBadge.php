<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblSoftDashWidgetBadge extends Model
{
    protected $table = 'tbl_soft_dash_widget_badge';
    protected $primaryKey = 'dash_widget_badge_id';


    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
}
