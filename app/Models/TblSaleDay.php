<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblSaleDay extends Model
{
    protected $table = 'tbl_sale_day';
    protected $primaryKey = 'day_id';
    protected $fillable = [
        'day_id',
        'voucher_id'
    ];


    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }

    public function dtl() {
        return $this->hasMany(TblSaleDayDtl::class, 'day_id')
            ->orderBy('sr_no','asc');
    }
    public function terminal() {
        return $this->belongsTo(TblSoftPOSTerminal::class, 'terminal_id');
    }
}
