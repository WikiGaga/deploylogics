<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblDefiContactType extends Model
{
    protected $table = 'tbl_defi_contact_type';
    protected $primaryKey = 'contact_type_id';


    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
}
