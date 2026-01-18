<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblBranchSyncState extends Model
{
    protected $table = 'branch_sync_state';
    protected $primaryKey = 'id';

    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }

    protected $fillable = [
        'id',
        'restaurant_id',
        'entity_type',
        'last_synced_at',
        'created_at',
        'updated_at',
    ];

    public $timestamps = false;
}

