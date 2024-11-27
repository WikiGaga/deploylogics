<?php

namespace App\Models\Defi;

use Illuminate\Database\Eloquent\Model;

class TblDefiSeason extends Model
{
    protected $table = 'tbl_defi_season';
    protected $primaryKey = 'season_id';
    protected $fillable = [
        'season_id',
        'season_name',
        'season_entry_status',
        'season_user_id',
        'business_id',
        'company_id',
        'branch_id'
    ];
    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
}
