<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblStgForm extends Model
{
    protected $table = 'tbl_stg_form';
    protected $primaryKey = 'STG_FORM_ID';

    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
}
