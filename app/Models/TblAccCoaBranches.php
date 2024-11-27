<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblAccCoaBranches extends Model
{
    protected $table = 'tbl_acco_chart_account_branches';
    protected $primaryKey = 'pk_id';

    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
}
