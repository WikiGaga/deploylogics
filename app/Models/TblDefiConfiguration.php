<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblDefiConfiguration extends Model
{
    protected $table = 'tbl_defi_configuration';
    protected $primaryKey = 'configuration_id';


    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
}
