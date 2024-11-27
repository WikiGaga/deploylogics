<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblSoftDashWidgetGraph extends Model
{
    protected $table = 'tbl_soft_dash_widget_graph';
    protected $primaryKey = 'dash_widget_graph_id';


    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
}
