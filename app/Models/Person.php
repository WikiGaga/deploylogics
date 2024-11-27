<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Person extends Model
{
    protected $table = 'persons';
    protected $primaryKey = 'person_id';
    protected $fillable = ['person_id','person_name','contact_name','address','country','city','postal_code'];
    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
    public function dtl(){
        return $this->hasMany(PersonDtl::class,"person_id");
    }
}
