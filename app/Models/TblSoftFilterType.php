<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblSoftFilterType extends Model
{
    protected $table = 'tbl_soft_filter_type';
    protected $primaryKey = 'filter_type_id';


    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
}
