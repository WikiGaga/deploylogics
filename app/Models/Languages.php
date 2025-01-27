<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Languages extends Model
{
    protected $table = 'tbllanguages';
    protected $primaryKey = 'code';
    public $incrementing = false;
    protected $keyType = 'string';

    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
}
