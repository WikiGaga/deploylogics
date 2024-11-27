<?php

namespace App\Models\Soft;

use Illuminate\Database\Eloquent\Model;

class TblSoftIpLocation extends Model
{
    protected $table = 'tbl_soft_ip_location';

    protected $primaryKey = 'ip_location_id';

    protected $fillable = [
        'ip_location_id',
        'ip_location_name',
        'ip_location_address',
        'ip_location_entry_status',
        'user_id',
        'business_id',
        'company_id',
        'branch_id',
        'created_at',
        'updated_at',
    ];

    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
}
