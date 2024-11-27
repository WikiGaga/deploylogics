<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PersonDtl extends Model
{
    protected $table = 'persons_dtl';
    protected $primaryKey = 'person_dtl_id';
    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
}
