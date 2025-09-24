<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class OptionsList extends Model
{
    protected $table = 'options_list';

    protected $primaryKey = 'id';

    protected static function primaryKeyName()
    {
        return (new static)->getKeyName();
    }

    // Define fillable fields if needed
    protected $fillable = [
        'name'
    ];

    // Define relationships if needed
    // For example, if options_list has relationships with other tables
}
