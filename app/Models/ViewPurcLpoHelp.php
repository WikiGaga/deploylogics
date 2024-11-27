<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ViewPurcLpoHelp extends Model
{
    protected $table = 'vw_purc_Lpo_help';
    protected $primaryKey = 'lpo_id';

    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
}
