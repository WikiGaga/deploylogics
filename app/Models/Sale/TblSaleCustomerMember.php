<?php

namespace App\Models\Sale;

use Illuminate\Database\Eloquent\Model;

class TblSaleCustomerMember extends Model
{
    protected $table = 'tbl_sale_customer_member';

    protected $primaryKey = 'customer_member_id';

    protected $fillable = [
        'customer_member_id',
        'customer_member_card_no',
        'membership_type_id',
        'issue_date',
        'expiry_date',
        'customer_member_status',
        'company_id',
        'branch_id',
        'business_id',
        'customer_member_user_id',
    ];
    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }
}
