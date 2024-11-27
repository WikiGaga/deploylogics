<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TempTestProduct extends Model
{
    protected $table = 'temp_test_product';

    protected $primaryKey = 'id';

    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
}
