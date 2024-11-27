<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ViewPurcLpoDetail extends Model
{
    protected $table = 'vw_purc_lpo_detail';
    protected $primaryKey = 'lpo_id';

    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
}
