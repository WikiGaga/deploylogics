<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblSoftProductTypeGroup extends Model
{
    protected $table = 'tbl_soft_product_type_group';
    protected $primaryKey = 'product_type_group_id';

    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
  
}
