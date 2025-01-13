<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblProductionConsumption extends Model
{
    protected $table = 'tblproductionconsumption';
    protected $primaryKey = 'code';

    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }

}
