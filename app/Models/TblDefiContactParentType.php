<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblDefiContactParentType extends Model
{
    protected $table = 'tbl_defi_contact_parent_type';
    protected $primaryKey = 'contact_parent_type_id';


    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
}
