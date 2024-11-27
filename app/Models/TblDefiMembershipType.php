<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblDefiMembershipType extends Model
{
    protected $table = 'tbl_defi_membership_type';
    protected $primaryKey = 'membership_type_id';


    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
}
