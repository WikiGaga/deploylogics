<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AddOn extends Model
{
    protected $table = 'add_ons';

    protected $primaryKey = 'id';

    protected $fillable = [
        'name',
        'price',
        'restaurant_id',
        'status'
    ];

    protected $casts = [
        'price' => 'float',
        'status' => 'integer',
        'restaurant_id' => 'integer'
    ];

    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }
}
