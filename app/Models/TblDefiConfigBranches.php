<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblDefiConfigBranches extends Model
{
    protected $table = 'tbl_defi_configuration_branches';
    protected $primaryKey = 'configuration_branches_id';


    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
}
