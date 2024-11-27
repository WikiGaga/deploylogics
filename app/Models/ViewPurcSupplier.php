<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ViewPurcSupplier extends Model
{
    protected $table = 'vw_purc_supplier';
    protected $primaryKey = 'supplier_id';

    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
}
