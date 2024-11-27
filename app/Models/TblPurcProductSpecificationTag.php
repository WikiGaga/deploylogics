<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblPurcProductSpecificationTag extends Model
{
    protected $table = 'tbl_purc_product_specification_tag';

    protected $primaryKey = 'specification_tag_id';

    protected $fillable = [
        'specification_tag_id',
        'tag_id',
        'product_id',
        'specification_tag_entry_status',
        'business_id',
        'company_id',
        'branch_id',
        'specification_tag_user_id',
    ];
}
