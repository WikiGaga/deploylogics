<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblSoftListingStudioJoinTable extends Model
{
    protected $table = 'tbl_soft_listing_studio_join_table';
    protected $primaryKey = 'listing_studio_join_table_id';

    protected $fillable = [
        'listing_studio_join_table_id',
        'listing_studio_id',
        'listing_studio_join_table_name',
        'listing_studio_join_table_sr_no',
        'listing_studio_join_table_column_name',
        'listing_studio_join_table_column_title',
        'business_id',
        'company_id',
        'branch_id',
        'listing_join_table_user_id',
        'listing_join_table_entry_status',
    ];

    protected $guarded = [
        'CREATED_AT',
        'UPDATED_AT',
    ];

    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
}
