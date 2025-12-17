<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeRole extends Model
{
    protected $table = 'employee_roles';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'name',
        'modules',
        'status',
        'restaurant_id'
    ];

    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
}

