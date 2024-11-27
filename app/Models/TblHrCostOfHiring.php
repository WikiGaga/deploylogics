<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblHrCostOfHiring extends Model
{
    protected $table = 'tbl_payr_cost_of_hiring';
    protected $primaryKey = 'cost_hiring_id';


    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
}
