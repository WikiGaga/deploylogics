<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblPayrPolicyCriteria extends Model
{
    protected $table = 'tbl_payr_policy_criteria';
    protected $primaryKey = 'criteria_id';

    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
}
