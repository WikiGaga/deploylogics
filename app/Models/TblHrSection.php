<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblHrSection extends Model
{
    protected $table = 'tbl_payr_section';
    protected $primaryKey = 'section_id';


    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }

    public function department(){
        return $this->belongsTo(TblHrDepartment::class, 'department_id');
    }
}
