<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblSoftFormCases extends Model
{
    protected $table = 'tbl_soft_form_cases';
    protected $primaryKey = 'form_cases_id';

    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
}
