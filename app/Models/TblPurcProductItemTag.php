<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblPurcProductItemTag extends Model
{
    protected $table = 'tbl_purc_product_item_tag';

    protected $primaryKey = 'item_tag_id';

    protected $fillable = [
        'item_tag_id',
        'tag_id',
        'product_id',
        'item_tag_entry_status',
        'business_id',
        'company_id',
        'branch_id',
        'item_tag_user_id',
    ];
}
