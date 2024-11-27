<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblHrSkills extends Model
{
    protected $table = 'tbl_payr_skills';
    protected $primaryKey = 'skill_id';


    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
}
