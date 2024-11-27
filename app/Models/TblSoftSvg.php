<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblSoftSvg extends Model
{
    protected $table = 'tbl_soft_svg';
    protected $primaryKey = 'svg_id';

    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
}
