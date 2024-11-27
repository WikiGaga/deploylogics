<?php

namespace App\Models\Soft;

use Illuminate\Database\Eloquent\Model;

class TblSoftUserIp extends Model
{
    protected $table = 'tbl_soft_user_ip';

    protected $primaryKey = 'user_ip_uuid';

    protected $fillable = [
        'user_ip_uuid',
        'ip_location_id',
        'ip_location_address',
        'user_id',
        'created_at',
        'updated_at',
    ];

    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
}
