<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblSaleDayDtl extends Model
{
    protected $table = 'tbl_sale_day_dtl';
    protected $primaryKey = 'day_dtl_id';

    protected $fillable = [
        'day_id',
        'shift_id',
        'day_dtl_id',
        'day_date',
        'to_date',
        'day_case_type',
        'saleman_id',
        'day_code',
        'sr_no',
        'document_name',
        'no_of_documents',
        'total_amount',
        'total_discount',
        'payment_mode',
        'opening_amount',
        'in_flow',
        'out_flow',
        'payment_mode_balance',
        'cash_in_hand_per_system',
        'closing_cash',
        'cash_difference',
        'transfer_amount',
        'pos_opening_amount',
        'cash_transfer_status',
        'notes',
        'created_at',
    ];

    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }

}
