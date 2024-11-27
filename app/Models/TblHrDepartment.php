<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblHrDepartment extends Model
{
    protected $table = 'tbl_payr_department';
    protected $primaryKey = 'department_id';


    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }

    public function sections(){
        return $this->hasMany(TblHrSection::class, 'section_id');
    }


}
