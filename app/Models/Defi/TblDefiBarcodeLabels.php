<?php

namespace App\Models\Defi;

use Illuminate\Database\Eloquent\Model;

class TblDefiBarcodeLabels extends Model
{
    protected $table = 'tbl_defi_barcode_labels';
    protected $primaryKey = 'barcode_labels_id';
    protected $fillable = [
        'barcode_labels_id',
        'barcode_labels_code',
        'barcode_labels_name',
        'barcode_labels_type',
        'business_id',
        'company_id',
        'branch_id',
        'barcode_labels_user_id',
        'barcode_labels_status',
        'created_at',
        'updated_at'
    ];
    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }

    function dtl(){
        return $this->hasMany(TblDefiBarcodeLabelsDtl::class, 'barcode_labels_id')->with('barcode');
    }
}
