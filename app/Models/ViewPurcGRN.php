<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ViewPurcGRN extends Model
{
    protected $table = 'vw_purc_grn';
    protected $primaryKey = 'grn_id';

    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
}
