<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ViewPurcDemand extends Model
{
    protected $table = 'vw_purc_demand';
    protected $primaryKey = 'demand_id';

    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }

    public function getCreatedbAtAttribute($date) {
        return date('d-m-Y',strtotime($date));
    }
}
